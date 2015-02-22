<?php

class UsersTableSeeder extends Seeder {

    public function run()
    {
        //DB::table('user_device')->delete();
        //DB::table('user')->delete();

        $users = array(
            array(
            	'full_name' => 'Ronen Cohen',
                'email'      => 'il.mrbit@gmail.com',
                'pass'   => Hash::make('yokozuna'),
                'created_at' => new DateTime,
                'updated_at' => new DateTime
            ),
			array(
				'full_name' => 'Moshe Aharonov',
				'email'      => 'dmw2007@gmail.com',
				'pass'   => Hash::make('yokozuna'),
				'created_at' => new DateTime,
				'updated_at' => new DateTime
			),
			array(
				'full_name' => 'Ido Lempert',
				'email'      => 'ido@lempert.co.il',
				'pass'   => Hash::make('yokozuna'),
				'created_at' => new DateTime,
				'updated_at' => new DateTime
			)
        );

        DB::table('user')->insert( $users );
    }

}

?>
