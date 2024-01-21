<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportRadiologiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_radiologies', function (Blueprint $table) {
            $table->id();
            $table->integer('report_id')->unsigned();
            $table->string('service_code');
            $table->string('service_title', 255)->nullable();
            $table->longText('result')->nullable();
            $table->dateTime('created_at');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_radiologies');
    }
}
