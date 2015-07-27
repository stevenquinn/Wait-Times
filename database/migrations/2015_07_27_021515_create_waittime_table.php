<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaittimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waittimes', function (Blueprint $table)
        {
	       $table->increments('id'); 
	       $table->dateTime('datetime');
	       $table->integer('ride_id')->unsigned();
	       $table->foreign('ride_id')->references('id')->on('rides');
	       $table->string('status');
	       $table->integer('wait');
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
        Schema::drop('waittimes');
    }
}
