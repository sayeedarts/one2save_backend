<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_medicines', function (Blueprint $table) {
            $table->id();
            $table->integer('report_id')->unsigned();
            $table->bigInteger('medplan_no')->nullable();
            $table->dateTime('medplan_date')->nullable();
            $table->bigInteger('item_code')->nullable();
            $table->string('item_name')->nullable();
            $table->text('notes')->nullable();
            $table->text('notes_ar')->nullable();
            $table->mediumText('remarks')->nullable();
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
        Schema::dropIfExists('report_medicines');
    }
}
