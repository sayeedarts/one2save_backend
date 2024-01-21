<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientTestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_test_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_test_id')->unsigned();
            $table->foreign('patient_test_id')->references('id')->on('patient_tests')->onDelete('cascade');
            // $table->unsignedInteger('service_id')->nullable();
            // $table->string('lab_profile')->nullable();
            $table->string('lab_test_name')->nullable();
            $table->string('lab_result')->nullable();
            $table->string('lab_units')->nullable();
            $table->string('lab_low')->nullable();
            $table->string('lab_high')->nullable();
            $table->string('lab_section')->nullable();
            $table->dateTime('tested_at')->nullable();
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
        Schema::dropIfExists('patient_test_details');
        // $table->dropForeign('lists_user_id_foreign');
        // $table->dropIndex('lists_user_id_index');
        // $table->dropColumn('user_id');
    }
}
