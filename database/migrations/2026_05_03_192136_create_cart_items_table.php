<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
            ->constrained()->onDelete('cascade');

            $table->foreignId('variant_id')
            ->constrained('product_variants')->onDelete('cascade');

            $table->foreignId('size_id')
            ->constrained('variant_sizes')->onDelete('cascade');

            $table->integer('quantity');

            $table->decimal('price',8,2);

            $table->unique(['user_id', 'variant_id', 'size_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
