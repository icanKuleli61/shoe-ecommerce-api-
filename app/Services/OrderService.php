<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\VariantSize;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BaseException;
use App\Enums\ErrorCode;

class OrderService
{
    /**
     * Kullanıcı sepetinden sipariş oluşturma ana akışı
     */
    public function createFromCart(array $data): Order
    {
        // 1. ADIM: Neon havuzunu yormamak için tüm OKUMA (SELECT) işlemlerini tekil JOIN'ler ile buraya topluyoruz.
        // Bu iki metot artık arkada sadece 1'er tane tertemiz SQL sorgusu çalıştırıyor.
        $cartItems = $this->getUserCart();
        $address = $this->getUserAddress($data['address_id']);

        // RAM üzerinde hesaplamalar
        $subtotal = $this->calculateSubtotal($cartItems);
        $shippingPrice = $this->calculateShipping($subtotal);
        $totalPrice = $subtotal + $shippingPrice;

        $this->validateStock($cartItems);
        $this->validatePaymentMethod($data['payment_method']);

        // 2. ADIM: Sadece milisaniyeler sürecek YAZMA (Insert/Update) işlemleri için transaction açıyoruz.
        return DB::transaction(function () use ($data, $address, $subtotal, $shippingPrice, $totalPrice, $cartItems) {

            // Siparişi Oluştur
            $order = $this->createOrder(
                data: $data,
                address: $address,
                subtotal: $subtotal,
                shippingPrice: $shippingPrice,
                totalPrice: $totalPrice
            );

            // Sipariş Maddelerini Toplu Ekle
            $this->createOrderItems(
                order: $order,
                cartItems: $cartItems
            );

            // Ödeme ve Stok Yönetimi
            $this->processPayment($order);
            $this->decreaseStocks($cartItems);
            $this->clearCart();

            return $order->load('items');
        });
    }

    /**
     * 🛠️ KESİNTİSİZ OPTİMİZE: Sepet ve ürün bilgilerini tek sorguda birleştirir.
     */
    protected function getUserCart()
    {
        $userId = auth()->id();

        if (!$userId) {
            throw new BaseException(ErrorCode::UNAUTHORIZED);
        }

        // 'product_variants.price as variant_price' ekledik ki hesaplamalar doğru çalışsın.
        $cartItems = CartItem::select(
            'cart_items.*',
            'products.name as product_name',
            'colors.name as color_name',
            'variant_sizes.size as size_value',
            'variant_sizes.stock as current_stock',
            'product_variants.price as variant_price' // Varyantın güncel fiyatını çektik
        )
            ->join('product_variants', 'cart_items.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('colors', 'product_variants.color_id', '=', 'colors.id')
            ->join('variant_sizes', 'cart_items.size_id', '=', 'variant_sizes.id')
            ->where('cart_items.user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            throw new BaseException(ErrorCode::EMPTY_CART);
        }

        return $cartItems;
    }

    /**
     * 🛠️ OPTİMİZE EDİLDİ: Adres ve konum bilgilerini tek bir SELECT sorgusunda JOIN ile getirir.
     */
    protected function getUserAddress(int $addressId): Address
    {
        // with() zincirini kırıp tek bir join sorgusuna çevirdik. Havuz kilitlenmesi önlendi.
        $address = Address::select(
            'addresses.*',
            'cities.name as city_name',
            'districts.name as district_name',
            'neighborhoods.name as neighborhood_name'
        )
            ->leftJoin('cities', 'addresses.city_id', '=', 'cities.id')
            ->leftJoin('districts', 'addresses.district_id', '=', 'districts.id')
            ->leftJoin('neighborhoods', 'addresses.neighborhood_id', '=', 'neighborhoods.id')
            ->where('addresses.user_id', auth()->id())
            ->where('addresses.id', $addressId)
            ->first();

        if (!$address) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        return $address;
    }

    /**
     * Sepet ara toplamını hesaplar
     */
    protected function calculateSubtotal($cartItems): float
    {
        // $item->price yerine join ile çektiğimiz variant_price alanını kullanıyoruz
        return $cartItems->sum(fn($item) => $item->variant_price * $item->quantity);
    }

    /**
     * Kargo ücretini hesaplar (3000 TL üzeri ücretsiz)
     */
    protected function calculateShipping(float $subtotal): float
    {
        return $subtotal >= 3000 ? 0 : 99;
    }

    /**
     * Stok durumunu kontrol eder
     */
    protected function validateStock($cartItems): void
    {
        foreach ($cartItems as $item) {
            if ($item->current_stock < $item->quantity) {
                throw new BaseException(ErrorCode::INSUFFICIENT_STOCK);
            }
        }
    }

    /**
     * Ödeme yöntemini doğrular
     */
    protected function validatePaymentMethod(string $paymentMethod): void
    {
        $allowedMethods = [
            Order::PAYMENT_METHOD_CARD,
            Order::PAYMENT_METHOD_WALLET
        ];

        if (!in_array($paymentMethod, $allowedMethods)) {
            throw new BaseException(ErrorCode::PAYMENT_FAILED);
        }
    }

    /**
     * Sipariş ana kaydını oluşturur
     */
    protected function createOrder(
        array $data,
        Address $address,
        float $subtotal,
        float $shippingPrice,
        float $totalPrice
    ): Order {
        return Order::create([
            'user_id' => auth()->id(),
            'address_id' => $address->id,
            'subtotal' => $subtotal,
            'shipping_price' => $shippingPrice,
            'total_price' => $totalPrice,
            'payment_method' => $data['payment_method'],
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'status' => Order::STATUS_PENDING,
            'full_name' => $address->full_name,
            'phone' => $address->phone,
            'city' => $address->city_name ?? '',      // Join'den gelen alias kullanıldı
            'district' => $address->district_name ?? '',  // Join'den gelen alias kullanıldı
            'neighborhood' => $address->neighborhood_name ?? '', // Join'den gelen alias kullanıldı
            'address_text' => $address->address,
        ]);
    }

    /**
     * Sipariş maddelerini bulk insert yapar
     */
    protected function createOrderItems(Order $order, $cartItems): void
    {
        $itemsData = [];
        $now = now();

        foreach ($cartItems as $item) {
            $itemsData[] = [
                'order_id' => $order->id,
                'variant_id' => $item->variant_id,
                'size_id' => $item->size_id,
                'product_name' => $item->product_name ?? 'Ürün',
                'variant_name' => $item->color_name,
                'size_value' => $item->size_value,
                'quantity' => $item->quantity,
                'price' => $item->variant_price, // Join ile çektiğimiz fiyat basılıyor
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        OrderItem::insert($itemsData);
    }

    /**
     * Ödeme tipine göre iş akışını yönlendirir
     */
    protected function processPayment(Order $order): void
    {
        if ($order->payment_method === Order::PAYMENT_METHOD_CARD) {
            $this->markOrderAsPaid($order);
            return;
        }

        $this->processWalletPayment($order);
    }

    /**
     * Cüzdan ödemesini işler
     */
    protected function processWalletPayment(Order $order): void
    {
        $wallet = auth()->user()->wallet;

        if (!$wallet) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        if ($wallet->balance < $order->total_price) {
            throw new BaseException(ErrorCode::INSUFFICIENT_BALANCE);
        }

        $newBalance = $wallet->balance - $order->total_price;
        $wallet->decrement('balance', $order->total_price);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'payment',
            'amount' => -$order->total_price,
            'current_balance' => $newBalance,
            'description' => 'Sipariş ödemesi yapıldı.',
            'reference_type' => 'order',
            'reference_id' => $order->id
        ]);

        $this->markOrderAsPaid($order);
    }

    /**
     * Siparişi ödendi olarak işaretler
     */
    protected function markOrderAsPaid(Order $order): void
    {
        $order->payment_status = Order::PAYMENT_STATUS_PAID;
        $order->save();
    }

    /**
     * Stok düşme işlemini doğrudan ID üzerinden update eder
     */
    protected function decreaseStocks($cartItems): void
    {
        foreach ($cartItems as $item) {
            VariantSize::where('id', $item->size_id)->decrement('stock', $item->quantity);
        }
    }

    /**
     * Sepeti temizler
     */
    protected function clearCart(): void
    {
        CartItem::where('user_id', auth()->id())->delete();
    }

    /**
     * Kullanıcının sipariş detayını gösterir
     */
    public function show($id): Order
    {
        $order = Order::with([
            'items.variant.images',
            'address',
            'user'
        ])
            ->where('user_id', auth()->id())
            ->find($id);

        if (!$order) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        return $order;
    }

    /**
     * Kullanıcının kendi sipariş geçmişini sayfalı listeler
     */
    public function index()
    {
        return Order::with([
            'items.variant.images'
        ])
            ->withCount('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
    }

    /**
     * Siparişin iptal edilebilir durumda olup olmadığını doğrular
     */
    protected function validateCancelableOrder(Order $order): void
    {
        $cancelableStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_APPROVED,
            Order::STATUS_SUPPLYING,
            Order::STATUS_PACKAGING
        ];

        if (!in_array($order->status, $cancelableStatuses)) {
            throw new BaseException(ErrorCode::BAD_REQUEST);
        }

        if ($order->status === Order::STATUS_CANCELLED) {
            throw new BaseException(ErrorCode::BAD_REQUEST);
        }
    }

    /**
     * Stokları iade eder
     */
    protected function restoreStocks(Order $order): void
    {
        foreach ($order->items as $item) {
            VariantSize::where('id', $item->size_id)->increment('stock', $item->quantity);
        }
    }

    /**
     * İptal edilen sipariş tutarını cüzdana iade eder
     */
    protected function refundWallet(Order $order): void
    {
        $wallet = $order->user?->wallet;

        if (!$wallet) {
            return;
        }

        $newBalance = $wallet->balance + $order->total_price;
        $wallet->increment('balance', $order->total_price);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => 'refund',
            'amount' => $order->total_price,
            'current_balance' => $newBalance,
            'description' => 'Sipariş iptal edildi. Ücret iadesi yapıldı.',
            'reference_type' => 'order',
            'reference_id' => $order->id
        ]);
    }

    /**
     * Sipariş durumunu İptal Edildi çeker
     */
    protected function markAsCancelled(Order $order): void
    {
        $order->status = Order::STATUS_CANCELLED;
        $order->save();
    }

    /**
     * Kullanıcı tarafından sipariş iptal akışı
     */
    public function cancel($id): Order
    {
        return DB::transaction(function () use ($id) {
            $order = Order::with('items')
                ->where('user_id', auth()->id())
                ->find($id);

            if (!$order) {
                throw new BaseException(ErrorCode::NOT_FOUND);
            }

            $this->validateCancelableOrder($order);
            $this->restoreStocks($order);
            $this->refundWallet($order);
            $this->markAsCancelled($order);

            return $order->fresh();
        });
    }

    /**
     * Kullanıcı tarafından kargosu teslim edilen siparişi tamamlama akışı
     */
    public function complete($id): Order
    {
        $order = Order::where('user_id', auth()->id())
            ->find($id);

        if (!$order) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        if ($order->status !== Order::STATUS_DELIVERED) {
            throw new BaseException(ErrorCode::BAD_REQUEST);
        }

        $order->status = Order::STATUS_COMPLETED;
        $order->save();

        return $order->fresh();
    }

    /**
     * Admin Paneli: Siparişleri filtreleme, arama ve listeleme akışı
     */
    public function adminIndex(array $filters)
    {
        $query = Order::with([
            'items.variant.images',
            'user'
        ])
            ->withCount('items');

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('email', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(20);
    }

    /**
     * Admin Paneli: Tekil sipariş detayı gösterme
     */
    public function adminShow($id): Order
    {
        $order = Order::with([
            'items.variant.images',
            'user'
        ])->find($id);

        if (!$order) {
            throw new BaseException(ErrorCode::NOT_FOUND);
        }

        return $order;
    }

    /**
     * Admin Paneli: Sipariş durumunu güncelleme ve state-flow yönetimi
     */
    public function adminUpdateStatus($id, string $status): Order
    {
        return DB::transaction(function () use ($id, $status) {
            $order = Order::with([
                'items',
                'user.wallet'
            ])->find($id);

            if (!$order) {
                throw new BaseException(ErrorCode::NOT_FOUND);
            }

            $allowedTransitions = Order::$statusFlow[$order->status];

            if (!in_array($status, $allowedTransitions)) {
                throw new BaseException(ErrorCode::BAD_REQUEST);
            }

            if ($status === Order::STATUS_CANCELLED) {
                $this->validateCancelableOrder($order);
                $this->restoreStocks($order);
                $this->refundWallet($order);
                $this->markAsCancelled($order);

                return $order->fresh();
            }

            $order->status = $status;
            $order->save();

            return $order->fresh();
        });
    }
}