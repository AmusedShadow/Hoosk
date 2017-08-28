<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_page_meta extends CI_Migration {
	protected $table = 'hoosk_page_meta';

	public function up() {
		Schema::create_table($this->table,function($table) {
			$table->auto_increment_integer('metaID');
			$table->integer('pageID');
			$table->text('pageKeywords');
			$table->text('pageDescription');
		});

		$this->seed();
	}

	public function down() {
		$this->dbforge->drop_table($this->table);
	}

	protected function seed() {
		$this->db->insert($this->table,array(
			'pageID' => 1,
			'pageKeywords' => 'Hoosk Keywords',
			'pageDescription' => 'Hoosk Description'
		));

		$this->db->insert($this->table,array(
			'pageID' => 2,
			'pageKeywords' => 'Contact',
			'pageDescription' => 'Contact'
		));

		$this->db->insert($this->table,array(
			'pageID' => 3,
			'pageKeywords' => 'test',
			'pageDescription' => 'test'
		));
	}
}