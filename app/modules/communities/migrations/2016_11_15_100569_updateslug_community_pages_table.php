<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateslugCommunityPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Schema::table('community_pages', function($table)
		// {
		// 	$table->string('name', 50)->default('');

		// });
		DB::statement('ALTER TABLE `community_pages` MODIFY COLUMN `slug` VARCHAR(50)');
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