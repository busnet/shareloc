<?php
class Device extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'device';
    public $timestamps = false;

    public function reports(){
        return $this->hasMany('DeviceReport', 'device_id')->orderBy('device_report.created_at', 'desc');
    }

    public function users(){
        return $this->belongsToMany('User', 'user_device', 'device_id', 'user_id');
    }
}