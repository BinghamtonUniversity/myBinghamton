<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagePreferencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('page_preferences', function(Blueprint $table) {
			//$table->increments('id');
			$table->integer('pidm')->unsigned();
			$table->string('page_id', 250)->default('');
			// $table->integer('layout')->default(0);
			// $table->string('name', 25)->default('');
			// $table->string('slug', 25)->default('');
			$table->mediumText('content');
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
		Schema::drop('page_preferences');
	}

}
