<?php

class GroupsTableSeeder extends Seeder {

	public function run()
	{
		// Uncomment the below to wipe the table clean before populating
		DB::table('groups')->truncate();

		$groups = array(
			array('group_id'=>'students', 'name'=>'Student Group', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_id'=>'freshman_class', 'name'=>'The Freshman Class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_id'=>'sophomore_class', 'name'=>'The Sophomore Class', 'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_id'=>'junior_class', 'name'=>'The Junior Class',  'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_id'=>'senior_class', 'name'=>'The Senior Class',  'created_at' => new DateTime,'updated_at' => new DateTime),
			array('group_id'=>'staff', 'name'=>'Staff Community' , 'created_at' => new DateTime,'updated_at' => new DateTime)
		);

		// Uncomment the below to run the seeder
		DB::table('groups')->insert($groups);

	}

}