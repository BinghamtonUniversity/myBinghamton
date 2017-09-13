<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupCompositesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_composites', function(Blueprint $table) {
			$table->integer('group_id');
			$table->integer('composite_id');
			$table->timestamps();
			//indexes
			// $table->primary(array('pidm', 'group_id'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_composites');
	}

}
