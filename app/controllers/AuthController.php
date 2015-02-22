<?php

class AuthController extends BaseController {

    public function status() {
        return Response::json(Auth::check());
    }

    public function secrets() {
        if(Auth::check()) {
            return 'You are logged in, here are secrets.';
        } else {
            return 'You aint logged in, no secrets for you.';
        }
    }

    public function register(){
        $userRules = array(
            'full_name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:user',
            'pass' => 'required|min:7|max:255',
        );

        $userData = array(
            'full_name' => Input::json('full_name'),
            'email' => Input::json('email'),
            'pass' => Input::json('pass'),
        );

        //$validator = Validator::make($userData, $userRules);
        //if ($validator->fails()) return Response::json(array('flash' => 'Register fieled!', 'errors' => $validator->failed()), 500);

        $deviceRules = array(
            'phone_no' => 'required|unique:device',
            'code' => 'required'
        );
        $deviceData = Input::json('device');

        //$validator = Validator::make($deviceData, $deviceRules);
        //if ($validator->fails()) return Response::json(array('flash' => 'Device not valid!', 'errors' => $validator->failed()), 500);

        $validator = Validator::make(array_merge($deviceData, $userData), array_merge($deviceRules, $userRules));
        if ($validator->fails()) return Response::json(array('flash' => 'Registration failed!', 'errors' => $validator->failed()), 500);

        //Create User
        $user = new User();
        $user->full_name = $userData['full_name'];
        $user->email = $userData['email'];
        $user->pass = Hash::make($userData['pass']);
        if (! $user->save()) return Response::json(array('flash' => 'Registration failed!'), 500);

        //Create device
        $device = new Device();
        $device->phone_no = $deviceData['phone_no'];
        $device->code = $deviceData['code'];
        if (! $user->devices()->save($device)) return Response::json(array('flash' => 'Device not saved!'), 500);

        Auth::login($user);
        return Response::json(Auth::user());
    }

    public function login()
    {
        if(Auth::attempt(array('email' => Input::json('email'), 'password' => Input::json('pass')), Input::json('remember')))
        {
            $user = Auth::user();
            $user->has_perm_to_approved = $user->getHasPermToApproved();

            return Response::json($user);
        } else {
            return Response::json(array('flash' => 'Invalid username or password'), 500);
        }
    }

    public function logout()
    {
        Auth::logout();
        return Response::json(array('flash' => 'Logged Out!'));
    }

    public function getLoggedInUser(){
        return Response::json(Auth::getUser());
    }

    public function isUnique(){
        $rules = array(
            'email' => 'required|email|unique:user'
        );

        $data = array(
            'email' => Input::json('email')
        );

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) return Response::json(null, 500);

        return Response::json(null);
    }
}
