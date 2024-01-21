<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->string('name_ar', 150)->nullable();
            $table->string('slug', 150)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('phone')->nullable();
            $table->string('tolfree', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->longText('details')->nullable();
            $table->string('facebook', 150)->nullable();
            $table->string('instagram', 150)->nullable();
            $table->string('twitter', 150)->nullable();
            $table->string('photo', 150)->nullable();
            $table->longText('gmap_iframe', 500)->nullable();
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
        Schema::dropIfExists('hospitals');
    }
}
