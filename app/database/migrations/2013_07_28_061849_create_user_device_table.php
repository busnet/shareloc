<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDeviceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_device', function(Blueprint $table)
		{
			$table->bigInteger('id', true, true);
			$table->bigInteger('user_id')->unsigned();
			$table->bigInteger('device_id')->unsigned();

            $table->unique(array('user_id', 'device_id'));

			$table->foreign('user_id')->references('id')->on('user');
			$table->foreign('device_id')->references('id')->on('device');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_device');
	}

}