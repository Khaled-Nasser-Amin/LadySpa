<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXsessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xsessions', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug');
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->integer('featured')->default(0);
            $table->string('image');
            $table->string('banner');
            $table->float('price',11,2);
            $table->float('sale',11,2)->nullable();
            $table->float('external_price',11,2)->nullable();
            $table->float('external_sale',11,2)->nullable();
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
        Schema::dropIfExists('xsessions');
    }
}
