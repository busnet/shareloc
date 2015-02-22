<?php

class UserController extends BaseController {
	public function getDevices()
	{
		return Response::json(Auth::user()->devices);
	}

    public function postDevice(){
        $user = Auth::user();

        $phoneNo = Input::json('phone_no');
        $code = Input::json('code');

        $device = Device::where('phone_no', $phoneNo)->where('code', $code)->first();
        if (!$device){
            $device = new Device();
            $device->phone_no = $phoneNo;
            $device->code = $code;
        }

        try{
            $device = $user->devices()->save($device);
        }catch (Exception $e){
            return Response::json(array('flash' => 'Device not added!'), 500);
        }

        return Response::json(array('flash' => 'Device Added!'));
    }

    public function postPlace(){
        $user = Auth::user();

        $name = Input::json('name');
        $lat = Input::json('lat');
        $long = Input::json('long');

        $place = new Place();
        $place->name = $name;
        $place->lat = $lat;
        $place->long = $long;


        try{
            $place = $user->places()->save($place);
        }catch (Exception $e){
            return Response::json(array('flash' => 'Place not added!'), 500);
        }

        return Response::json($place);
    }
    public function deletePlace($id){
        $user = Auth::user();

        $success = Place::where('user_id', $user->id)
            ->where('id', $id)
            ->delete();

        return $success ? Response::json(array('flash' => 'Place removed')) : Response::json(array('flash' => 'Place not added!'), 500);
    }

    public function getPlaces(){
        $user = Auth::user();

        $places = $user->places()->get();

        return Response::json($places);
    }


    public function postNearbyAlert(){
        $user = Auth::user();

        $nearbyAlert = new NearbyAlert;

        $nearbyAlert->user_id = $user->id;
        $nearbyAlert->device_id = Input::json('device_id');
        $nearbyAlert->place_id = Input::json('place_id');
        $nearbyAlert->distance = Input::json('distance');

        $nearbyAlert->save();

        return Response::json($nearbyAlert);
    }


    public function deleteNearbyAlert($id){
        $user = Auth::user();

        $success = NearbyAlert::where('user_id', $user->id)
            ->where('id', $id)
            ->delete();

        return $success ? Response::json(array('flash' => 'Alert removed')) : Response::json(array('flash' => 'Alert not added!'), 500);
    }

    public function getNearbyAlerts(){
        $user = Auth::user();
        $deviceId = Input::get('device_id');


        $q = NearbyAlert::select('nearby_alert.*', 'place.name')
            ->where('nearby_alert.user_id', $user->id)
            ->join('place', 'place.id', '=', 'nearby_alert.place_id');

        if ($deviceId) $q->where('device_id', $deviceId);

        $nearbyAlerts = $q->get();

        $nearbyAlertId2nearbyAlert = array();
        $nearbyAlertIds = array();
        foreach($nearbyAlerts as $nearbyAlert){
            $nearbyAlertIds[] = $nearbyAlert->id;
            $nearbyAlertId2nearbyAlert[$nearbyAlert->id] = $nearbyAlert;
        }

        $nearbyAlertNotifications = DB::select('SELECT x.* FROM nearby_alert_notification x JOIN (SELECT nearby_alert_id, MAX(visited_at) max_n FROM nearby_alert_notification GROUP BY nearby_alert_id) y ON y.nearby_alert_id = x.nearby_alert_id AND y.max_n = x.visited_at AND x.nearby_alert_id IN (?);', array(implode(',', $nearbyAlertIds)));

        foreach ($nearbyAlertNotifications as $nearbyAlertNotification){
            $nearbyAlertId2nearbyAlert[$nearbyAlertNotification->nearby_alert_id]->visited_at = $nearbyAlertNotification->visited_at ? $nearbyAlertNotification->visited_at : null;
            $nearbyAlertId2nearbyAlert[$nearbyAlertNotification->nearby_alert_id]->left_at = $nearbyAlertNotification->left_at ? $nearbyAlertNotification->left_at : null;
        }


        return Response::json($nearbyAlerts);
    }
}