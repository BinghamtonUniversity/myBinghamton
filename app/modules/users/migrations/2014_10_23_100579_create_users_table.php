<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->integer('pidm')->unsigned();
			$table->string('bnum', 20)->default('');
			$table->string('first_name', 50)->default('');
			$table->string('last_name', 50)->default('');
			$table->string('email', 100)->default('');
			$table->integer('invalidate')->default(0);
			$table->rememberToken();
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
		Schema::drop('users');
	}

}
