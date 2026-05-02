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
        Schema::create('variant_sizes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')
            ->constrained('product_variants')->onDelete('cascade');

            $table->integer('size');

            $table->integer('stock')->default(0);

            $table->decimal('price', 8, 2);

            $table->unique(['variant_id', 'size']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_sizes');
    }
};
