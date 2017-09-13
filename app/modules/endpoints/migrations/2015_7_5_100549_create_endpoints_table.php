<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEndpointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('endpoints', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 25)->default('');
			$table->string('target', 100)->default('');
			$table->string('authtype', 50)->default('');
			$table->string('username', 50)->default('');
			$table->string('password', 512)->default('');
			$table->integer('group_id');
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
		Schema::drop('endpoints');
	}

}

