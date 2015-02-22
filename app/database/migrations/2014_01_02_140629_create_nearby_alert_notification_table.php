<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNearbyAlertNotificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('nearby_alert_notification', function(Blueprint $table)
        {
            $table->bigInteger('id', true, true);

            $table->bigInteger('nearby_alert_id')->unsigned();

            $table->dateTime('visited_at');
            $table->dateTime('left_at')->nullable();

            $table->foreign('nearby_alert_id')->references('id')->on('nearby_alert');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nearby_alert_notification');
    }

}