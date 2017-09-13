<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupVisitsPerDaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_visits_per_days', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('group_id')->unsigned();
			$table->integer('num_sessions');
			$table->integer('unique_visits');
			$table->double('avg_session_length',12,4);
			$table->string('day', 25);
			$table->date('date');
			$table->timestamps();
			$table->foreign('group_id')->references('id')->on('groups');
			$table->index('group_id');
			$table->index('day');
			$table->index('date');

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
