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
            $table->string('reference')->unique();
            $table->string('brand')->nullable();
            $table->foreignId('provider_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('instagrammer_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['INSTOCK', 'OUTSTOCK']);
            $table->enum('category', ['Clothing', 'Accessoiries','Home','Sport','Beauty','Electronics','Pets']);
            $table->enum('echantillon', ['FREE', 'PAID','REFUNDED']);
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
