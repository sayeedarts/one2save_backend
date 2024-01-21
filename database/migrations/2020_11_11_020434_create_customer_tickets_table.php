<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('type', 60)->nullable();
            $table->string('name', 255)->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('email', 150)->nullable();
            $table->longText('details')->nullable();
            $table->foreignId('user_id');
            $table->string('ip_address', 50)->nullable();
            $table->string('user_agent', 255)->nullable();
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
        Schema::dropIfExists('customer_tickets');
    }
}
