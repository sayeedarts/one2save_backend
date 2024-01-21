<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->string('slug', 150)->nullable();
            $table->string('image', 150)->nullable();
            $table->longText('details')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('hospital_id');
            $table->foreignId('department_id');
            $table->bigInteger('phone')->nullable();
            $table->string('email', 150)->nullable();
            $table->longText('qualifications')->nullable();
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
        Schema::dropIfExists('doctors');
    }
}
