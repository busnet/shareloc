<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('device', function(Blueprint $table)
		{
			$table->bigInteger('id', true, true);
			$table->string('phone_no');
			$table->string('code');

            $table->unique(array('phone_no', 'code'));

			$table->index('phone_no');
			$table->index('code');
            $table->index('device_type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('device');
	}

}