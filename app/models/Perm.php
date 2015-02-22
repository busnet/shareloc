<?php

class Perm extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'perm';
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('User', 'user_id');
    }

    public function grantedUser(){
        return $this->belongsTo('User', 'granted_user_id');
    }

    public function device(){
        return $this->belongsTo('Device');
    }

    public function getCreatedAtAttribute($value){
        return $value ? strtotime($value) * 1000 : null;
    }

    public function getApprovedAtAttribute($value){
        return $value ? strtotime($value) * 1000 : null;
    }

    public function getDisabledAtAttribute($value){
        return $value ? strtotime($value) * 1000 : null;
    }
}