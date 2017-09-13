<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ExtendCommunityPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('community_pages', function($table)
		{
			$table->integer('device')->default(0);
			$table->string('groups')->default('');
			$table->mediumText('mobile_order');
			$table->timestamp('meta_updated_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Schema::drop('poll_submissions');
	}

}