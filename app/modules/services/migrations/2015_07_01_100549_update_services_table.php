<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('services', function($table)
		{
			$table->mediumText('script')->nullable();
			$table->mediumText('sources')->nullable();
    	$table->dropColumn('path');
    	$table->dropColumn('data_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Schema::drop('services');
	}

}
