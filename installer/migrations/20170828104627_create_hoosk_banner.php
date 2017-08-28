<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_banner extends CI_Migration {
	protected $table = 'hoosk_banner';

	public function up() {
		Schema::create_table($this->table,function($table) {
			$table->auto_increment_integer('slideID');
			$table->integer('pageID');
			$table->string('slideImage',350);
			$table->string('slideLink',350);
			$table->string('slideAlt',350);
			$table->integer('slideOrder');
		});
	}

	public function down() {
		$this->dbforge->drop_table($this->table);
	}
}