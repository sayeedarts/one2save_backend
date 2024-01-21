<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCareerRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('career_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('career_id')->nullable();
            $table->string('firstname', 150)->nullable();
            $table->string('lastname', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->bigInteger('phone')->nullable();
            $table->char('gender', 10)->nullable();
            $table->unsignedInteger('nationality')->nullable();
            $table->string('file', 150)->nullable();
            $table->foreignId('user_id');
            $table->char('status')->nullable();
            $table->unsignedInteger('processed_by')->nullable();
            $table->string('rejection_details', 500)->nullable();
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
        Schema::dropIfExists('career_requests');
    }
}
