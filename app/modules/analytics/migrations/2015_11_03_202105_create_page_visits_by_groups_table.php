<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageVisitsByGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('page_visits_by_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('group_id')->unsigned();
			$table->integer('page_id')->unsigned();
			$table->date('date');
			$table->integer('unique_visits');
			$table->timestamps();
			$table->foreign('group_id')->references('id')->on('groups');
			$table->foreign('page_id')->references('id')->on('community_pages');
			$table->index('group_id');
			$table->index('page_id');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
