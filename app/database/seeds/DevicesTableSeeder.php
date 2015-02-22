<?php

class DevicesTableSeeder extends Seeder {
    public function run()
    {
        $userA = User::where('email', 'il.mrbit@gmail.com')->first();
        $deviceA = new Device(array(
            'phone_no' => '972532730276',
            'code' => 'AAABBBCCCDDD'
        ));
        $userA->devices()->save($deviceA);

        $userB = User::where('email', 'dmw2007@gmail.com')->first();
        $deviceB = new Device(array(
            'phone_no' => '972546534700',
            'code' => 'BBBBBBBBBBBB'
        ));
        $userB->devices()->save($deviceB);

        $userC = User::where('email', 'ido@lempert.co.il')->first();
        $deviceC = new Device(array(
            'phone_no' => '972546534701',
            'code' => 'CCCCCCCCCCCCCC'
        ));
        $userC->devices()->save($deviceC);
    }
}

?>
