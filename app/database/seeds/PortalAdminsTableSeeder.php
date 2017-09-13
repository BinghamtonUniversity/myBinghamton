<?php

class PortalAdminsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('portal_admins')->truncate();

		$portal_admins = array(
			array('portal_pidm'=>'1', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('portal_pidm'=>'10', 'created_at' => new DateTime,'updated_at' => new DateTime)
		);

		// Uncomment the below to run the seeder
		DB::table('portal_admins')->insert($portal_admins);

	}

}
