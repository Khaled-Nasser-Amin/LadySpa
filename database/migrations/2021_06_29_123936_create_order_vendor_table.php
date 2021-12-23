<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('users');
            $table->float('taxes',11,2);
            $table->float('subtotal',11,2);
            $table->float('total_amount',11,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_vendor');

    }
}
