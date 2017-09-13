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

		$this->call('GroupsTableSeeder');
		$this->call('GroupMembersTableSeeder');
		$this->call('GroupAdminsTableSeeder');
		$this->call('PortalAdminsTableSeeder');
		// $this->call('UserTableSeeder');
	}

}
