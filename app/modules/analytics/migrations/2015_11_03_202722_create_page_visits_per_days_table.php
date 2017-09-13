<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageVisitsPerDaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('page_visits_per_days', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('page_id')->unsigned();
			$table->string('day', 25);
			$table->date('date');
			$table->integer('num_visits');
			$table->integer('num_bounces');
			$table->double('avg_time',8,4);
			$table->timestamps();
			$table->foreign('page_id')->references('id')->on('community_pages');
			$table->index('page_id');
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
