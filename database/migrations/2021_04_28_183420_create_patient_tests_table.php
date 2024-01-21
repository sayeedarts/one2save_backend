<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_tests', function (Blueprint $table) {
            $table->id();
            $table->integer('report_id')->unsigned();
            // $table->integer('patient_id')->unsigned();
            // $table->integer('hospital_id')->unsigned();
            // $table->string('mrn', 100)->nullable();
            // $table->unsignedInteger('visit_id')->nullable();
            $table->unsignedInteger('request_id')->nullable();
            $table->unsignedInteger('service_id')->nullable();
            $table->string('lab_profile')->nullable();
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
        Schema::dropIfExists('lab_details');
    }
}
