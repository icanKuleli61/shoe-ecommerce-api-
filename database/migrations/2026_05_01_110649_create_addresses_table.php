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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();


            $table->foreignId('user_id')
            ->constrained()->onDelete('cascade');

             $table->foreignId('city_id')
            ->constrained()->onDelete('cascade');

            
            $table->foreignId('district_id')
            ->constrained()->onDelete('cascade');

        
            $table->foreignId('neighborhood_id')
            ->constrained()->onDelete('cascade');

   
            $table->text('address');

            $table->string('title')->nullable();

            $table->boolean('is_default')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
