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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('quantity');
            $table->decimal('priceSale');
            $table->decimal('priceFav')->nullable();
            $table->decimal('priceMax')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('instagrammer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['Available', 'Unavailable']);
            $table->enum('category', ['Clothing', 'Accessoiries','Home','Sport','Beauty','Electronics','Pets']);
            $table->boolean('FreeEchantillon')->nullable()->default(true);
            $table->timestamps();
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
