<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_post_category extends CI_Migration {
    protected $table = 'hoosk_post_category';

    public function up() {
        Schema::create_table($this->table, function ($table) {
            $table->auto_increment_integer('categoryID');
            $table->text('categoryTitle');
            $table->text('categorySlug');
            $table->text('categoryDescription');
        });

        $this->seed();
    }

    public function down() {
        $this->dbforge->drop_table($this->table);
    }

    protected function seed() {
        $this->db->insert($this->table, array(
            'categoryTitle'       => 'Uncategorized',
            'categorySlug'        => 'uncategorized-asd',
            'categoryDescription' => 'This is the default category for things that dont quite fit anywhere',
        ));

        $this->db->insert($this->table, array(
            'categoryTitle'       => 'Hoosk Updates',
            'categorySlug'        => 'hoosk_updates',
            'categoryDescription' => 'Latest hoosk updates',
        ));

        $this->db->insert($this->table, array(
            'categoryTitle'       => 'FAQs',
            'categorySlug'        => 'faqs',
            'categoryDescription' => 'Hoosk FAQs',
        ));

        $this->db->insert($this->table, array(
            'categoryTitle'       => 'Test Category',
            'categorySlug'        => 'test',
            'categoryDescription' => 'test',
        ));
    }
}