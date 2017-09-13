<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupAdminsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_admins', function(Blueprint $table) {
			$table->integer('pidm')->unsigned();
			$table->integer('group_id');
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
		Schema::drop('group_admins');
	}

}
