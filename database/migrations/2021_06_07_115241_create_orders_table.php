<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('payment_way');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('customers')->onDelete('cascade');
            $table->float('subtotal',11,2)->nullable();
            $table->float('total_amount',11,2)->nullable();
            $table->float('shipping',11,2)->nullable();
            $table->float('taxes',11,2)->nullable();
            $table->float('discount',11,2)->nullable();
            $table->string('order_status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->string('address');
            $table->tinyInteger('hold')->default(0);
            $table->string('lat_long');
            $table->string('description');
            $table->string('receiver_phone');
            $table->string('receiver_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
