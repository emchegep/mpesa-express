<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_request_id')->unique();
            $table->string('checkout_request_id')->unique();
            $table->string('phone_number');
            $table->double('amount',6,2);
            $table->string('account_reference');
            $table->string('transaction_desc');
            $table->string('status');
            $table->string('mpesa_receipt_number')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('transaction_date')->nullable();
            $table->timestamps();
        });
    }
};
