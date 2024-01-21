<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_visits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('mrn')->nullable();
            $table->bigInteger('patient_id')->nullable();
            $table->bigInteger('ref_id');
            $table->dateTime('date');
            $table->bigInteger('doctor_id');
            $table->string('doctor_name')->nullable();
            $table->tinyInteger('leave_requested')->default(0);
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
        Schema::dropIfExists('patient_visits');
    }
}
