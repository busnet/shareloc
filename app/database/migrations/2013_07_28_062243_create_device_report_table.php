<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceReportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('device_report', function(Blueprint $table)
		{
			$table->bigInteger('id', true, true);
			
			$table->bigInteger('device_id')->unsigned();
			$table->float('lat', 16, 14);
			$table->float('long', 16, 14);
			$table->float('accuracy');
			$table->dateTime('created_at');
			
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
		Schema::drop('device_report');
	}

}