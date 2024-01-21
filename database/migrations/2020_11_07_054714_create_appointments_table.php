<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->char('mrn', 60);
            $table->foreignId('hospital_id')->index();
            $table->foreignId('department_id')->index();
            $table->foreignId('doctor_id')->index();
            $table->date('date');
            $table->time('time');
            $table->unsignedBigInteger('is_cancelled')->nullable();
            $table->unsignedBigInteger('is_suggested')->nullable();
            $table->unsignedBigInteger('suggested_by')->nullable();
            $table->mediumText('suggestion')->nullable();
            $table->char('payment_type', 10);
            // $table->char('patient_type', 10);
            // $table->string('existing_mrn', 100);
            $table->foreignId('user_id')->index();
            $table->tinyInteger('source')->default(0)->comment('0 is for internal and 1 is for outside source');
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
        Schema::dropIfExists('appointments');
    }
}
