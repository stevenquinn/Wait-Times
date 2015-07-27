<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParRideTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rides', function (Blueprint $table)
        {
	       $table->increments('id');
	       $table->string('name');
	       $table->string('api_name');
	       $table->integer('park_id')->unsigned();
	       $table->foreign('park_id')->references('id')->on('parks');
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
        Schema::drop('rides');
    }
}
