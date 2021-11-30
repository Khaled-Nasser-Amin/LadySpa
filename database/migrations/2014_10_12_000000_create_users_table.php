<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->default('vendor');
            $table->string('geoLocation');
            $table->string('store_name')->unique();
            $table->tinyInteger('add_product')->default(0);
            $table->string('location');
            $table->string('phone')->unique();
            $table->string('whatsapp')->unique();
            $table->string('code')->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->tinyInteger('activation')->default(0);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
