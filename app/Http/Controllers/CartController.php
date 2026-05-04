<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;

class CartController extends Controller
{
    protected CartService $service;

    protected function __construct(CartService $service){
        $this->service = $service;
    }

    public function add(AddToCartRequest  $request)
    {
        $cart = $this->service->add($request->validated());

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    /**
     * 🧾 Sepeti getir
     */
    public function index()
    {
        $cart = $this->service->get();

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    /**
     * 🔄 Güncelle
     */
    public function update(UpdateCartItemRequest $request, $id)
    {
        $cart = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    /**
     * ❌ Sil
     */
    public function destroy($id)
    {
        $this->service->remove($id);

        return response()->json([
            'success' => true,
            'message' => 'Sepetten silindi'
        ]);
    }

    /**
     * 🧹 Sepeti temizle
     */
    public function clear()
    {
        $this->service->clear();

        return response()->json([
            'success' => true,
            'message' => 'Sepet temizlendi'
        ]);
    }
    
}
