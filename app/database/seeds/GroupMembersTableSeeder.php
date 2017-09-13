<?php

class GroupMembersTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('group_members')->truncate();

		$group_members = array(
			array('group_pidm'=>'1', 'group_id'=>'student_group', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'2', 'group_id'=>'freshman_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'3', 'group_id'=>'junior_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'4', 'group_id'=>'freshman_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'5', 'group_id'=>'freshman_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'6', 'group_id'=>'staff', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'7', 'group_id'=>'staff', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'8', 'group_id'=>'freshman_class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'9', 'group_id'=>'student_group', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_pidm'=>'10', 'group_id'=>'student_group', 'created_at' => new DateTime,'updated_at' => new DateTime),
		);

		// Uncomment the below to run the seeder
		DB::table('group_members')->insert($group_members);

	}

}
