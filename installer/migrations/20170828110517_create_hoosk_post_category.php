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
        $data = array(
            array(
                'categoryTitle'       => 'Uncategorized',
                'categorySlug'        => 'uncategorized-asd',
                'categoryDescription' => 'This is the default category for things that dont quite fit anywhere',
            ),
            array(
                'categoryTitle'       => 'Hoosk Updates',
                'categorySlug'        => 'hoosk_updates',
                'categoryDescription' => 'Latest hoosk updates',
            ),
            array(
                'categoryTitle'       => 'FAQs',
                'categorySlug'        => 'faqs',
                'categoryDescription' => 'Hoosk FAQs',
            ),
            array(
                'categoryTitle'       => 'Test Category',
                'categorySlug'        => 'test',
                'categoryDescription' => 'test',
            ),
        );

        foreach ($data as $insert) {
            $query = $this->db->from($this->table);
            foreach ($insert as $name => $value) {
                $query->where($name, $value);
            }

            $query = $query->get();
            if ($query->num_rows() == 0) {
                $this->db->insert($this->table, $insert);
            }
        }
    }
}