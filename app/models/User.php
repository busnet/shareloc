<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('pass');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->pass;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

    public function devices(){
        return $this->belongsToMany('Device', 'user_device', 'user_id', 'device_id');
    }

    public function places(){
        return $this->hasMany('Place', 'user_id');
    }

    public function nearbyAlerts(){
        return $this->hasMany('NearbyAlert', 'user_id');
    }

    public function perms(){
        return $this->hasMany('Perm', 'user_id');
    }

    public function followers(){
        return DB::table('perm')
            ->select(array(
                'perm.id',
                'perm.device_id',
                'f_user.full_name',
                'device.phone_no',
                'device.code',
                'perm.created_at',
                'perm.approved_at',
                'perm.disabled_at',
            ))
            ->join('device', 'perm.device_id', '=', 'device.id')
            ->join('user_device', 'perm.device_id', '=', 'user_device.device_id')
            ->join('user', 'user_device.user_id', '=', 'user.id')
            ->join('user AS f_user', 'perm.user_id', '=', 'f_user.id')
            ->where('user.id', '=', $this->id)

            ->orderBy('perm.created_at', 'desc')
            ->groupBy('perm.id');
    }

    public function getFollowingDevices(){
        return DB::select('SELECT d.id, d.phone_no, d.device_type, p.nickname, dr.lat, dr.`long`, dr.created_at, p.approved_at, p.disabled_at  FROM perm p JOIN device d ON d.id = p.device_id LEFT JOIN (SELECT dr . * FROM `device_report` dr JOIN max_device_report mdr ON mdr.id = dr.id) AS dr ON dr.device_id = p.device_id WHERE p.user_id = ' . ((int) $this->id));

//        return DB::table('perm')
//            ->select('device.id', 'device.phone_no', 'perm.nickname', 'device_report.lat', 'device_report.long', 'device_report.created_at', 'perm.approved_at', 'perm.disabled_at')
//            ->where('perm.user_id', '=', $this->id)
//            ->join('device', 'device.id', '=', 'perm.device_id')
//            ->leftJoin('device_report', 'device_report.device_id', '=', db::raw('device.id AND device_report.id IN (SELECT MAX(dr.id) FROM device_report dr GROUP BY dr.device_id)'));
    }

    public function getAllDevices(){
        return DB::select('SELECT * FROM device_report dr, device d WHERE d.id = dr.device_id GROUP BY dr.device_id ');
    }

    public function followingDevice(){
        return DB::table('perm')
            ->select('device.id', 'device.phone_no', 'perm.nickname', 'device_report.lat', 'device_report.long', 'device_report.created_at', 'perm.approved_at', 'perm.disabled_at')
            ->where('perm.user_id', '=', $this->id)
            ->join('device', 'device.id', '=', 'perm.device_id')
            ->leftJoin('device_report', 'device_report.device_id', '=', 'device.id')
            ->whereIn('device_report.id', function($q){
                $q->select(db::raw('MAX(dr.id)'))
                    ->from('device_report AS dr')
                    ->groupBy('dr.device_id');
            });
    }

    public function getHasPermToApproved(){
        $perm = Perm::join('user_device', 'user_device.id', '=', 'perm.device_id')
            ->where('user_device.user_id', '=', $this->id)
            ->whereNull('perm.approved_at')
            ->first();

        return $perm ? true : false;
    }
}