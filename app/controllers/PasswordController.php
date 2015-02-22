<?php

class PasswordController extends BaseController {
    public function postRemind(){
        Session::remove('error');

        $credentials = array('email' => Input::json('email'));

        Password::remind($credentials, function($message, $user){
            $message->subject('Your Password Reminder');
        });

        if (Session::has('error')) return Response::json(array('flash' => trans(Session::get('reason'))), 500);
        return Response::json(array('flash' => 'An e-mail with the password reset has been sent.'));
    }

    public function postReset(){
        Session::remove('error');

        $passReminder = DB::table('password_reminders')->where('token', Input::get('token'))->first();
        if (!$passReminder) return Response::json(array('flash' => 'This password reset token is invalid. '), 500);

        $credentials = array('email' => $passReminder->email);

        Password::reset($credentials, function($user, $password){
            $user->pass = Hash::make($password);
            $user->save();
        });

        if (Session::has('error')) return Response::json(array('flash' => trans(Session::get('reason'))), 500);
        return Response::json(array('flash' => 'Password has been successfully reset. Please login using your Shareloc appication'));
    }
}