<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_members', function(Blueprint $table) {
			$table->integer('pidm')->unsigned();
			$table->integer('group_id');
			$table->string('messaging_pref', 25)->default('');
			$table->string('membership_status', 25)->default('');
			$table->string('last_updated_by', 25)->default('');
			$table->timestamps();
			//indexes
			$table->primary(array('pidm', 'group_id'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_members');
	}

}
