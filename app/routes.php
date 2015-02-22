<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Route::get('/test', function(){
//    Event::listen('illuminate.query', function($sql, $params){
//        var_dump($sql);
//        var_dump($params);
//    });
//});

//Route::get('/test', function(){
//    $nearbyAlerts = NearbyAlert::select('nearby_alert.id', 'place.lat', 'place.long')
//        ->where('device_id', 3)
//        ->join('place', 'place.id', '=', 'nearby_alert.place_id')
//        ->get();
//
//    echo '<pre>';
//    var_dump($nearbyAlerts[0]);
//    echo '</pre>';
//});

$funSinglePage = function() {
    return View::make('singlepage');
};

$paths = array('/', '/home', '/login', '/logout', '/register', '/remind', '/reset/{token}', '/terms', '/dashboard', '/add-device', '/following-devices-map', '/following-devices-list', '/add-following-device', '/followers', '/following-device/{id}');
foreach ($paths as $path){
    Route::get($path, $funSinglePage);
}

Route::group(array('before' => 'auth'), function(){
    Route::get('/user/devices', 'UserController@getDevices');
    Route::get('/auth/loggedInUser', 'AuthController@getLoggedInUser');
    Route::post('/user/device', 'UserController@postDevice');

    Route::get('/user/places', 'UserController@getPlaces');
    Route::post('/user/place', 'UserController@postPlace');
    Route::delete('/user/place/{id}', 'UserController@deletePlace');
    Route::post('/user/nearbyAlert', 'UserController@postNearbyAlert');
    Route::get('/user/nearbyAlerts', 'UserController@getNearbyAlerts');
    Route::delete('/user/nearbyAlert/{id}', 'UserController@deleteNearbyAlert');

    Route::get('/perm/followers', 'PermController@getFollowers');
    Route::get('/perm/followingDevice/{id}', 'PermController@getFollowingDevice');
    Route::get('/perm/followingDevices', 'PermController@getFollowingDevices');
    Route::post('/perm/followingDevice', 'PermController@postFollowingDevice');
    Route::put('/perm/activeFollowingDevice', 'PermController@putActiveFollowingDevice');
    Route::delete('/perm/followingDevice', 'PermController@deleteFollowingDevice');
    Route::delete('/perm/device/{id}', 'PermController@deleteDevice');


});

Route::group(array('before' => 'csrf_json'), function(){
    Route::post('/auth/register', 'AuthController@register');
    Route::post('/auth/login', 'AuthController@login');
});


Route::post('/password/remind', 'PasswordController@postRemind');
Route::post('/password/reset', 'PasswordController@postReset');

Route::get('/auth/logout', 'AuthController@logout');
Route::get('/auth/status', 'AuthController@status');
Route::get('/auth/secrets','AuthController@secrets');

Route::post('/auth/isUnique', 'AuthController@isUnique');

Route::post('/device/report', 'DeviceController@postReport');
Route::get('/device/reportSMS', 'DeviceController@postReportSms');
Route::post('/device/reportDevice', 'DeviceController@postReportDevice');


//Test new device
Route::any('/device/log', function(){
    $data = Input::all();
    Log::info('Device Report', array('context' => json_encode($data)));
});
