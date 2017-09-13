<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('users')->truncate();

		$users = array(
			array('pidm'=>'588358', 'first_name'=>'Adam','last_name'=>'Smallcomb', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('pidm'=>'520689', 'first_name'=>'Tim','last_name'=>'Cortesi', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('pidm'=>'278615', 'first_name'=>'Madhuri','last_name'=>'Govindaraju', 'created_at' => new DateTime,'updated_at' => new DateTime)
		);


		// Uncomment the below to run the seeder
		DB::table('users')->insert($users);

	}

}
