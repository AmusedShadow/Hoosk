<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_blog extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            'blog_id'          => array(
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
            ),
            'blog_title'       => array(
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ),
            'blog_description' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('blog_id', TRUE);
        $this->dbforge->create_table('blog');

        Schema::create_table('hoosk_banner', function ($table) {
            $table->auto_increment_integer('slideID');
            $table->bigint('pageID');
            $table->string('slideImage', 350);
            $table->string('slideLink', 350);
            $table->string('slideAlt', 350);
            $table->integer('slideOrder');
        });
    }

    public function down() {
        $this->dbforge->drop_table('blog');
    }
}