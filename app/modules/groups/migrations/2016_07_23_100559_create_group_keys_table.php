<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupKeysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_keys', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('group_id');
			$table->string('name', 25)->default('');
			$table->string('value', 25)->default('');
			$table->timestamps();
			// //indexes
			// $table->primary(array('group_pidm','group_id'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_keys');
	}

}
