<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNearbyAlertTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nearby_alert', function(Blueprint $table)
        {
            $table->bigInteger('id', true, true);

            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('device_id')->unsigned();
            $table->bigInteger('place_id')->unsigned();

            $table->integer('distance');

            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('device_id')->references('id')->on('device');
            $table->foreign('place_id')->references('id')->on('place');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nearby_alert');
    }

}