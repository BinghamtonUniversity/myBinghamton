<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFormsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('custom_forms', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('group_id');
			$table->string('gs_id', 100)->default('');
			$table->string('name', 100)->default('');
			$table->string('email', 100)->default('');
			$table->string('target', 255)->default('');
			$table->string('fields', 500)->default('');
			$table->string('options', 500)->default('');
			// $table->boolean('shuffle')->default('0');
			$table->timestamps();
			//indexes
			// $table->primary('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('custom_forms');
	}

}