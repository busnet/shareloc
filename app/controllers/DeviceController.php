<?php

class DeviceController extends BaseController {
    private function distance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);

        $meters = $dist * 60 * 1.1515 * 1609.34;
        return $meters;
    }

    /**
     * Convert to Decimal
     * Parse $string "3448.9052", 34 - degrees, 48 - minutes(should be multiplied by  60), 9052 * 6/1000,
     * and then to calculate the whole number, for example : 34.8150867
     * @param $string
     * @return string
     */
    public function convertToDec($string){
        $deg = null;
        $sec = null;

        $prop = explode(".",abs($string));

        $deg = substr( $prop[0], 0, 2);
        $min = substr( $prop[0], 2);
        $sec = ( $prop[1] * 6 / 1000);

        $result = $deg + (((number_format($min, 6)*60)+(number_format($sec, 6)))/3600);

        return number_format($result, 7);
    }

    /** Parse SMS from device
     * @smsTextExample [$string = "+972544225502;DATE:14/03/12,TIME:07/17/22,LAT:32.0931983N,LOT:034.8143916E,Speed:046.8,31-21,234,42502-7923-3339"]
     * @param $string
     * @return array
     */
    public function parseDataSMS($string){
        $stringExplode = explode(';',$string);

        $expStrings = explode(',',$stringExplode[1]);
        $result = array();
        foreach ( $expStrings as $expString ) {
            $expProp = explode(':',$expString);
            if ( isset($expProp[0]) && isset($expProp[1]) ){
                if ( $expProp[0] == 'LAT' ){
                    $expProp[1] = abs($expProp[1]);
                }

                if($expProp[0] == 'LOT'){
                    $expProp[1] = abs($expProp[1]);
                }

                $result[$expProp[0]] = $expProp[1];
            }
        }

        //TODO: check if device exist
        if(isset($stringExplode[0])){
            $result['CODE'] = $stringExplode[0];
        }

        return $result;
    }

    /**
     * @param $code
     * @param $lat
     * @param $long
     * @param $accuracy
     * @param null $userId
     * @param $deviceType
     * @return \Illuminate\Http\JsonResponse
     */
    public function commonDeviceReport($code, $lat, $long, $accuracy, $userId=null, $deviceType){

        $device = Device::where('code', '=', $code)
            ->orderBy('id', 'desc')
            ->first();

        //hard coded userId of nigeria user
        if($userId == "97"  && !$device){
            $device = new Device();
            $device->phone_no = $code;
            $device->code = $code;
            $device->device_type = $deviceType;
            $device->save();

            $perm = new Perm();
            $perm->user_id      = $userId;
            $perm->device_id    = $device->id;
            $perm->nickname     = $code;
            $perm->created_at   = new DateTime;
            $perm->approved_at  = new DateTime;
            $perm->save();

        }

        if ($device && $lat && $long && $accuracy){
            $now = new DateTime;

            $deviceReport = new DeviceReport();

            $deviceReport->device_id = $device->id;
            $deviceReport->lat = $lat;
            $deviceReport->long = $long;
            $deviceReport->accuracy = $accuracy;
            $deviceReport->created_at = $now;

            $deviceReport->save();

            //handle notifications
            $nearbyAlerts = NearbyAlert::select('nearby_alert.id', 'nearby_alert.distance', 'place.lat', 'place.long')
                ->where('device_id', $device->id)
                ->join('place', 'place.id', '=', 'nearby_alert.place_id')
                ->get();

            $allNearbyAlertIds = array();
            $nearbyAlertIds = array();
            foreach($nearbyAlerts as $nearbyAlert){
                //echo $this->distance($nearbyAlert->lat, $nearbyAlert->long, $lat, $long) . '|' . $nearbyAlert->distance;
                if ($this->distance($nearbyAlert->lat, $nearbyAlert->long, $lat, $long) <= $nearbyAlert->distance){
                    $nearbyAlertIds[] = $nearbyAlert->id;
                }

                $allNearbyAlertIds[] = $nearbyAlert->id;
            }


            $openNearbyAlertIds = array();
            $leftNearbyAlertIds = array();            $currentNotifications = DB::select('SELECT x.* FROM nearby_alert_notification x JOIN (SELECT nearby_alert_id, MAX(visited_at) max_n FROM nearby_alert_notification GROUP BY nearby_alert_id) y ON y.nearby_alert_id = x.nearby_alert_id AND y.max_n = x.visited_at AND x.left_at IS NULL AND x.nearby_alert_id IN (?);', array(implode(',', $allNearbyAlertIds)));
            foreach($currentNotifications as $currentNotification){
                if (! in_array($currentNotification->nearby_alert_id, $nearbyAlertIds)){
                    $leftNearbyAlertIds[] = $currentNotification->nearby_alert_id;
                } else {
                    $openNearbyAlertIds[] = $currentNotification->nearby_alert_id;
                }
            }

//            print_r($leftNearbyAlertIds);
//            print_r($openNearbyAlertIds);
//            die();

            //Update left at
            if (count($leftNearbyAlertIds)) {
                DB::table('nearby_alert_notification')
                    ->whereNull('left_at')
                    ->whereIn('nearby_alert_id', $leftNearbyAlertIds)
                    ->update(array('left_at' => $now));
            }

            if (count($nearbyAlertIds)) {
                $nearbyAlertIds = array_diff($nearbyAlertIds, $openNearbyAlertIds);

                $notifications = array();
                foreach($nearbyAlertIds as $nearbyAlertId){
                    $notifications[] = array(
                        'nearby_alert_id' => $nearbyAlertId,
                        'visited_at' => $now
                    );

                }

                //create new notifications
                if (count($notifications)) DB::table('nearby_alert_notification')->insert($notifications);
            }

            return Response::json(array('flash' => 'Device reported'));
        }
    }

    /**
     * report from device
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReport(){
        //Log::info("Enter post report" . json_encode($_POST));
        $code = Input::get('code');
        $lat = Input::get('lat');
        $long = Input::get('long');
        $accuracy = Input::get('accuracy', 10);

        $deviceType = 'phone';

        try{
            return $this->commonDeviceReport($code, $lat, $long, $accuracy, null, $deviceType);
        }catch (Exception $e){

        }

        return Response::json(array('flash' => 'Device not reported'), 500);
	}

    /**
     * report from special device
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReportDevice(){
        //$string ="SLU354660046599935,06,10,131104151105,01,131104151105,+3205.6136,+03448.9052,001.6,259,000127,31031,13113,12.232,00.645,1,2*2B";
        //32.09350 34.8152
        $buf    = Input::get('buf');
        $userId = Input::get('userId');

        $expString = explode(",", $buf);

        $code = $expString[0];
        $lat  = $this->convertToDec($expString[6]);
        $long = $this->convertToDec($expString[7]);
        $accuracy = $expString[4];

        $deviceType = 'car';

        try{
            return $this->commonDeviceReport($code, $lat, $long, $accuracy, $userId, $deviceType);
        }catch (Exception $e){
        }

        return Response::json(array('flash' => 'Device not reported new'), 500);
    }

    /**
     * report from sms server
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReportSms(){
        $text = Input::get('text');

        $parsedData = $this->parseDataSMS($text);

        $deviceType = 'human';

        //TODO: $accuracy missing to report
        try{
            return $this->commonDeviceReport($parsedData["CODE"], $parsedData["LAT"], $parsedData["LOT"],"10", null, $deviceType);
        }catch (Exception $e){
        }

        return Response::json(array('flash' => 'Device not reported new'), 500);
    }
}