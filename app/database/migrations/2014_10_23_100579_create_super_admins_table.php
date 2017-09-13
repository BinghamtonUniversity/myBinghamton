<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuperAdminsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('super_admins', function(Blueprint $table) {
			$table->integer('pidm')->unsigned();
			$table->string('last_updated_by', 25)->default('');
			$table->string('app', 25)->default('');
			$table->timestamps();
			//indexes
			$table->primary(array('pidm', 'app'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('super_admins');
	}

}
