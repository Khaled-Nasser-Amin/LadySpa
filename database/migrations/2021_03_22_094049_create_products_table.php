<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug');
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->integer('featured')->default(0);
            $table->integer('featured_slider')->default(0);
            $table->string('image');
            $table->string('banner');
            $table->string('type');
            $table->float('group_price',11,2)->nullable();
            $table->float('group_sale',11,2)->default(0);
            $table->string('reviews')->default(0);
            $table->tinyInteger('isActive')->default(1);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('products');
    }
}
