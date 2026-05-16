<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');


            $table->foreignId('address_id')
                ->nullable()
                ->constrained('addresses')
                ->nullOnDelete();

            $table->decimal(
                'subtotal',
                10,
                2
            );

            $table->decimal(
                'shipping_price',
                10,
                2
            )->default(0);

            $table->decimal(
                'total_price',
                10,
                2
            );

            $table->enum('payment_method', [

                'card',
                'wallet'

            ])->default('card');

            $table->enum('payment_status', [

                'pending',
                'paid',
                'failed'

            ])->default('pending');

            $table->enum('status', [

                'pending',

                'approved',

                'supplying',

                'packaging',

                'shipped',

                'out_for_delivery',

                'delivered',

                'completed',

                'cancelled'

            ])->default('pending');

            $table->string('full_name');

            $table->string('phone');

            $table->string('city');

            $table->string('district');

            $table->string('neighborhood');

            $table->text('address_text');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};