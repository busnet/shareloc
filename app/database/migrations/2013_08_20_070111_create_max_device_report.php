<?php

use Illuminate\Database\Migrations\Migration;

class CreateMaxDeviceReport extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('CREATE VIEW max_device_report AS SELECT MAX(id) AS id, device_id FROM device_report GROUP BY device_report.device_id');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('DROP TABLE max_device_report;');
	}

}