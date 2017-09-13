<?php

class GroupAdminsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('group_admins')->truncate();

		$group_admins = array(
			array('admin_pidm'=>'1', 'group_id'=>'student_group', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('admin_pidm'=>'2', 'group_id'=>'freshman_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('admin_pidm'=>'3', 'group_id'=>'junior_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('admin_pidm'=>'4', 'group_id'=>'freshman_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('admin_pidm'=>'5', 'group_id'=>'staff', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('admin_pidm'=>'16', 'group_id'=>'staff', 'created_at' => new DateTime,'updated_at' => new DateTime),
		);

		// Uncomment the below to run the seeder
		DB::table('group_admins')->insert($group_admins);

	}

}
