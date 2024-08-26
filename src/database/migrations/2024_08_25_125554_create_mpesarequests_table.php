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
        Schema::create('mpesarequests', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable();
            $table->double('amount',8,2)->nullable();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->string('MerchantRequestID')->nullable()->unique();
            $table->string('CheckoutRequestID')->nullable()->unique();
            $table->string('status')->nullable();
            $table->string('MpesaReceiptNumber')->nullable();
            $table->string('ResultDesc')->nullable();
            $table->string('TransactionDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesarequests');
    }
};
