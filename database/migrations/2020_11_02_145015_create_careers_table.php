<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCareersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150)->nullable();
            $table->foreignId('hospital_id');
            $table->char('job_type', 60)->nullable();
            $table->string('field', 150)->nullable();
            $table->string('level', 150)->nullable();
            $table->date('publish_on', 150)->nullable();
            $table->longText('details')->nullable();
            $table->string('resume', 150)->nullable();
            $table->foreignId('user_id');
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
        Schema::dropIfExists('careers');
    }
}
