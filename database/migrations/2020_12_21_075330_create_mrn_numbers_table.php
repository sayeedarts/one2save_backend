<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMrnNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mrn_numbers', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id')->unsigned();
            // $table->foreign('patient_id')->references('id')->on('patients');
            $table->integer('hospital_id')->unsigned();
            // $table->foreign('hospital_id')->references('id')->on('hospitals');
            $table->string('mrn', 100)->nullable();
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
        Schema::dropIfExists('mrn_numbers');
    }
}
