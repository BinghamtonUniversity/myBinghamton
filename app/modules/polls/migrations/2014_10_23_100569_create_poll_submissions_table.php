<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePollSubmissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('poll_submissions', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('poll_id');
			$table->integer('pidm')->unsigned();
			$table->integer('choice');
			//$table->string('last_updated_by', 25)->default('');
			$table->timestamps();
			//indexes
			// $table->primary('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('poll_submissions');
	}

}
