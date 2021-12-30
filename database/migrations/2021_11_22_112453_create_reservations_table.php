<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('payment_way');
            $table->float('subtotal',11,2)->nullable();
            $table->float('total_amount',11,2)->nullable();
            $table->float('shipping',11,2)->nullable();
            $table->float('taxes',11,2)->nullable();
            $table->string('reservation_status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->string('address');
            $table->string('receiver_phone');
            $table->string('receiver_name');
            $table->string('lat_long');
            $table->string('type');


            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('customers');

            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('users');

            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('xsessions');

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
        Schema::dropIfExists('reservations');
    }
}
