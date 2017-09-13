<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('services', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('group_id');
			$table->string('name', 25)->default('');
			$table->string('data_type', 25)->default('');
			$table->string('path', 100)->default('');
			$table->mediumText('template')->nullable();
			$table->mediumText('css')->nullable();
			// $table->boolean('container')->default(false);
			$table->timestamps();
			//indexes
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('services');
	}

}
