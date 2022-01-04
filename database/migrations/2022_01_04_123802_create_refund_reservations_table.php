<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('users');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('xsessions');
            $table->integer('number_of_additions')->nullable();
            $table->integer('number_of_persons');
            $table->float('total_refund_amount',11,2);
            $table->float('subtotal_refund_amount',11,2);
            $table->float('taxes',11,2)->nullable();
            $table->string('refund_status')->default('not refunded yet');
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
        Schema::dropIfExists('refund_reservations');
    }
}
