<?php

class DeviceReport extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'device_report';
    public $timestamps = false;

    public function getCreatedAtAttribute($value){
        return $value ? strtotime($value) * 1000 : null;
    }
}