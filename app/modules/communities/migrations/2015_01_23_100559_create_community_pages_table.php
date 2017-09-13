<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommunityPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('community_pages', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('group_id');
			$table->integer('layout')->default(0);
			$table->string('name', 25)->default('');
			$table->string('slug', 25)->default('');
			$table->integer('order')->default(2147483647);
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
		Schema::drop('community_pages');
	}

}
