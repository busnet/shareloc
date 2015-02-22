<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

        DB::table('perm')->delete();
        DB::table('user_device')->delete();
        DB::table('device_report')->delete();
        DB::table('device')->delete();
        DB::table('user')->delete();

		$this->call('UsersTableSeeder');
		$this->call('DevicesTableSeeder');
        $this->call('PermsTableSeeder');
        $this->call('DeviceReportsTableSeeder');
	}

}
