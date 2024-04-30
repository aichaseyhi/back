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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('firstName');
            $table->string('secondName');
            $table->string('email');
            $table->string('phone');
            $table->string('city');
            $table->integer('post_code');
            $table->integer('cardNumber');
            $table->integer('securityCode');
            $table->string('CVV');
            $table->integer('quantity');
            $table->float('totalPrice');
            $table->enum('', ['PENDING', 'ACCEPTED','REFUSED','CANCEL'])->default('PENDING');
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
