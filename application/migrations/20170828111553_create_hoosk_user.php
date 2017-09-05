<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_user extends CI_Migration {
    protected $table = 'hoosk_user';

    public function up() {
        Schema::create_table($this->table, function ($table) {
            $table->auto_increment_integer('userID');
            $table->string('userName', 15);
            $table->string('email', 250);
            $table->string('password', 250);
            $table->string('RS', 15);
        });
    }

    public function down() {
        $this->dbforge->drop_table($this->table);
    }
}