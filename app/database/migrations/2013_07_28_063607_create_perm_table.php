<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('perm', function(Blueprint $table)
		{
			$table->bigInteger('id', true, true);

			$table->bigInteger('user_id')->unsigned();
			$table->bigInteger('device_id')->unsigned();
            $table->string('nickname');
			$table->dateTime('created_at');
            $table->dateTime('approved_at')->nullable();
			$table->dateTime('disabled_at')->nullable();

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
		Schema::drop('perm');
	}

}