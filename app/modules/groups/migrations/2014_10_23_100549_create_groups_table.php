<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 500)->default('');
			$table->string('type', 25)->default('');
			$table->string('slug', 500)->default('');
			$table->boolean('community_flag')->default('0');
			$table->boolean('priority')->default('0');
			$table->string('last_updated_by', 25)->default('');
			$table->integer('order')->default(2147483647);
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
		Schema::drop('groups');
	}

}
