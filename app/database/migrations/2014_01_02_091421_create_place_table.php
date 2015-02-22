<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('place', function(Blueprint $table)
        {
            $table->bigInteger('id', true, true);

            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->float('lat', 16, 14);
            $table->float('long', 16, 14);

            $table->foreign('user_id')->references('id')->on('user');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('place');
	}

}