<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname', 150)->nullable();
            $table->string('secondname', 150)->nullable();
            $table->string('thirdname', 150)->nullable();
            $table->string('lastname', 150)->nullable();
            $table->string('mrn', 100)->nullable();
            $table->bigInteger('phone')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('hospital_id');
            $table->unsignedBigInteger('created_by');
            $table->char('gender', 10)->nullable();
            $table->string('national_id', 60)->nullable();
            $table->longText('rejection_reason', 500)->nullable();
            $table->string('national_id_type', 60)->nullable();
            $table->string('religion', 60)->nullable();
            $table->unsignedInteger('nationality')->nullable();
            $table->tinyInteger('approved')->default(0);
            $table->date('dob')->nullable();
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
        Schema::dropIfExists('patients');
    }
}
