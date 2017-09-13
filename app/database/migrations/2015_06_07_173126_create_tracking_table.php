<?php

use Illuminate\Database\Migrations\Migration;

class CreateTrackingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('visits', function($t)
		{			
			$t->increments('id');			
			$t->integer('pidm')->unsigned();
			$t->string('path', 250)->default('');
			$t->string('referrer', 250)->default('');
			$t->integer('width')->unsigned('');
			$t->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}
}
