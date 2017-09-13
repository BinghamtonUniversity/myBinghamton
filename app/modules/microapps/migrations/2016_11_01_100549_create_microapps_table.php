<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMicroappsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('microapps', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('group_id');
			$table->string('name', 50)->default('');
			$table->mediumText('template')->nullable();
			$table->mediumText('css')->nullable();
			$table->mediumText('options')->nullable();
			$table->mediumText('script')->nullable();
			$table->mediumText('sources')->nullable();
			// $table->boolean('container')->default(false);
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
		Schema::drop('microapps');
	}

}
