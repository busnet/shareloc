<?php
class Place extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'place';
    public $timestamps = false;

    public function users(){
        return $this->hasMany('User', 'device_id');
    }
}