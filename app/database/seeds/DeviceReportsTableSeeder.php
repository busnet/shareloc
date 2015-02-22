<?php

class DeviceReportsTableSeeder extends Seeder {

    public function run()
    {
        $devices = Device::all();

        $data = array();
        foreach ($devices as $i => $device){
            $data[] = array(
                'device_id' => $device->id,
                'lat' => 32.066158 + ($i * 0.01),
                'long' => 34.777819 + ($i * 0.01),
                'accuracy' => rand(1,10),
                'created_at' => new DateTime
            );

            $data[] = array(
                'device_id' => $device->id,
                'lat' => 32.964648 + ($i * 0.01),
                'long' => 35.495997 + ($i * 0.01),
                'accuracy' => rand(1,10),
                'created_at' => new DateTime
            );

            $data[] = array(
                'device_id' => $device->id,
                'lat' => 29.557669 + ($i * 0.01),
                'long' => 34.951925 + ($i * 0.01),
                'accuracy' => rand(1,10),
                'created_at' => new DateTime
            );
        }

        DeviceReport::insert($data);
    }

}

?>
