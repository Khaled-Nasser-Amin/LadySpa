<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text('payment_token')->nullable();
            $table->string('place_limit')->nullable();
            $table->string('twillo_token')->nullable();
            $table->string('twillo_phone')->nullable();
            $table->string('twillo_sid')->nullable();
            $table->integer('contact_land_line')->nullable();
            $table->integer('contact_whatsapp')->nullable();
            $table->integer('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->float('shipping_cost_by_kilometer')->default(100);
            $table->string('shipping_status')->default('by_kilometer');
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
        Schema::dropIfExists('settings');
    }
}
