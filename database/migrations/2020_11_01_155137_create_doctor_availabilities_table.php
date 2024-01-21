<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_availabilities', function (Blueprint $table) {
            $table->foreignId('doctor_id');
            $table->string('day', 50);
            $table->string('duty_type', 50)->nullable();
            $table->char('from', 10)->nullable();
            $table->char('to', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor_availabilities');
    }
}
