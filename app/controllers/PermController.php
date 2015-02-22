<?php

class PermController extends BaseController {
    public function getFollowers(){
        $user = Auth::user();

        try{
            $followers = $user->followers()->get();
            //var_dump($followers);
            //die();
            foreach($followers as &$follower){
                if ($follower->created_at) $follower->created_at = strtotime($follower->created_at) * 1000;
                if ($follower->approved_at) $follower->approved_at = strtotime($follower->approved_at) * 1000;
                if ($follower->disabled_at) $follower->disabled_at = strtotime($follower->disabled_at) * 1000;

                if ($follower->approved_at && !$follower->disabled_at) $follower->is_active = true;
                else $follower->is_active = false;
            }

        } catch (Exception $e){
            return Response::json(array('flash' => 'Can not get followers'), 500);
        }

        return Response::json($followers);
    }

    public function getFollowingDevice($id){
        $followingDevice = null;

        $user = Auth::user();

        //If user admin select device with out report
        if($user->id == 104){
            $dq = Device::where('id', $id);
            $dq->with(array('reports' => function($q){$q->take(1);}));

            $followingDevice = $dq->first();
            $followingDevice->nickname = $dq->first()->phone_no;
            if ($followingDevice) return Response::json($followingDevice);
        }

        $perm = Perm::where('perm.device_id', '=', $id)
            ->where('perm.user_id', '=', $user->id)
            ->first();


        if ($perm){
            $dq = Device::where('id', $id);

            if ($perm && $perm->approved_at && is_null($perm->disabled_at)) {
                $dq->with(array('reports' => function($q){
                    $q->take(1);
                }));
            }

            $followingDevice = $dq->first();
            $followingDevice->nickname = $perm->nickname;
        }

        if ($followingDevice) return Response::json($followingDevice);
        return Response::json(array('flash' => 'Device not exist!'), 500);
    }

    public function deleteDevice($id){
        $user = Auth::user();

        if($user->id == 104){
            $success = DeviceReport::where('device_id', $id)->delete();
        } else {
            $success = Perm::where('user_id', $user->id)->where('device_id', $id)->delete();
        }

        if ($success) return Response::json(array('flash' => 'Device deleted'));
        return Response::json(array('flash' => 'Device not deleted'), 500);
    }

    public function getFollowingDevices(){
        $user = Auth::user();

        $followingDevices = array();

        try {
            //User Administrator
            if ($user->id == 104) {
                $followingDevices = $user->getAllDevices();

                foreach ($followingDevices as &$followingDevice) {
                    if ($followingDevice->created_at) $followingDevice->created_at = strtotime($followingDevice->created_at) * 1000;
                }
            } else {
                $followingDevices = $user->getFollowingDevices();

                foreach ($followingDevices as &$followingDevice) {
                    if ($followingDevice->created_at) $followingDevice->created_at = strtotime($followingDevice->created_at) * 1000;
                    if ($followingDevice->approved_at) $followingDevice->approved_at = strtotime($followingDevice->approved_at) * 1000;
                    if ($followingDevice->disabled_at) $followingDevice->disabled_at = strtotime($followingDevice->disabled_at) * 1000;

                    if (!$followingDevice->approved_at) $followingDevice->created_at = null;
                    if ($followingDevice->disabled_at || !$followingDevice->approved_at) {
                        $followingDevice->lat = null;
                        $followingDevice->long = null;
                    }
                }
            }

        } catch (Exception $e) {
            return Response::json(array('flash' => 'Device not Found!'), 500);
        }

        return Response::json($followingDevices);
    }

    //add device if  not exist from postFollowingDevice()
    /*public function postDevice($phoneNumber, $deviceCode){
        $user = Auth::user();

        $phoneNo = $phoneNumber;
        $code = $deviceCode;

        $device = Device::where('phone_no', $phoneNo)->where('code', $code)->first();
        if (!$device){
            $device = new Device();
            $device->phone_no = $phoneNo;
            $device->code = $code;
        }

        try{
            $device = $user->devices()->save($device);
        }catch (Exception $e){
            return false;
        }

        return $device;
    }*/

	public function postFollowingDevice(){
        $data = array(
            'phone_no' => Input::json('phone_no'),
            'nickname' => Input::json('nickname')/*,
            'code'     => Input::json('code')*/
        );

        $rules = array(
            'phone_no' => 'required|exists:device,phone_no',
            'nickname' => 'required'
        );

        $validator = Validator::make($data, $rules);
        if ($validator->fails() && empty($data['code'])) return Response::json(array('flash' => 'Device not added', 'errors' => $validator->failed()), 500);

        $user = Auth::user();

        /*if(!empty($data['code'])){
            $newDevice = $this->postDevice($data['phone_no'], $data['code']);

            if($newDevice){
                $perm = new Perm();
                $perm->user_id      = $user->id;
                $perm->device_id    = $newDevice->id;
                $perm->nickname     = $data['nickname'];
                $perm->created_at   = new DateTime;
                $perm->approved_at  = new DateTime;

                try{
                    if($perm->save()) return Response::json('Device added and approved');
                }catch (Exception $e){
                    //Duplicate entery
                }
            }
        }*/

        $device = Device::join('user_device', 'user_device.device_id', '=', 'device.id')
            ->where('user_device.user_id', '!=', $user->id)
            ->where('device.phone_no', '=', $data['phone_no'])
            ->first();

        if ($device){
            $perm = new Perm();
            $perm->user_id = $user->id;
            $perm->device_id = $device->id;
            $perm->nickname = $data['nickname'];
            $perm->created_at = new DateTime;

            try {
                if ($perm->save()) return Response::json('Device added');
            }catch(Exception $e) {
               //Duplicate entery
            }
        }

        return Response::json(array('flash' => 'Device not added'), 500);
    }

    public function putActiveFollowingDevice(){
        $user = Auth::user();

        $permId = Input::json('id');
        $isActive = Input::json('is_active');

        $perm = Perm::select('perm.*')
            ->join('device', 'perm.device_id', '=', 'device.id')
            ->join('user_device', 'device.id', '=', 'user_device.device_id')
            ->where('perm.id', '=', $permId)
            ->where('user_device.user_id', '=', $user->id)
            ->first();

        if ($perm){
            if ($isActive) {
                if (!$perm->approved_at) $perm->approved_at = new DateTime;
                $perm->disabled_at = null;
            } else {
                $perm->disabled_at = new DateTime;
            }

            if ($perm->save()) return Response::json(array('flash' => 'Permission changed!'));
        }

        return Response::json(array('flash' => 'Permission not changed!'), 500);
    }

    public function deleteFollowingDevice(){
        $user = Auth::user();

        $ids = Input::json('ids');

        $affectedRows = DB::delete('DELETE perm.* FROM perm JOIN user_device ON user_device.device_id = perm.device_id WHERE perm.id IN (?) AND user_device.user_id = ?', array(implode(',', $ids), $user->id));
//        $affectedRows = Perm::join('user_device', 'user_device.device_id', '=', 'perm.device_id')
//            ->where('user_device.user_id', '=', $user->id)
//            ->whereIn('perm.id', $ids)
//            ->delete();

        if ($affectedRows) return Response::json(array('flash' => 'Following devices deleted!'));
        return Response::json(array('flash' => 'Following devices not deleted!'), 500);
    }
}