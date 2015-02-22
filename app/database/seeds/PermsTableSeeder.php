<?php

class PermsTableSeeder extends Seeder {

    public function run()
    {
        $userA = User::where('email', 'il.mrbit@gmail.com')->first();
        $deviceA =  $userA->devices()->first();

        $userB = User::where('email', 'dmw2007@gmail.com')->first();
        $deviceB = $userB->devices()->first();

        $userC = User::where('email', 'ido@lempert.co.il')->first();
        $deviceC = $userC->devices()->first();

        $data = array(
            array('user_id' => $userA->id, 'device_id' => $deviceB->id, 'nickname' => "Ido Bit's Phone", 'approved_at' => new DateTime),
            array('user_id' => $userB->id, 'device_id' => $deviceC->id, 'nickname' => "Ronen's Phone", 'approved_at' => new DateTime),
            array('user_id' => $userC->id, 'device_id' => $deviceA->id, 'nickname' => "Ido's Phone", 'approved_at' => new DateTime),
        );

        Perm::insert($data);
    }
}

?>
