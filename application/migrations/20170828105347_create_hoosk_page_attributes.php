<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_page_attributes extends CI_Migration {
    protected $table = 'hoosk_page_attributes';

    public function up() {
        Schema::create_table($this->table, function ($table) {
            $table->auto_increment_integer('pageID');
            $table->integer('pagePublished');
            $table->integer('pageParent');
            $table->string('pageTemplate', 250);
            $table->integer('pageBanner');
            $table->string('pageURL', 250);
            $table->integer('enableJumbotron');
            $table->integer('enableSlider');
            $table->integer('enableSearch');
            $table->timestamp('pageUpdated');
        });

        $this->seed();
    }

    public function down() {
        $this->dbforge->drop_table($this->table);
    }

    protected function seed() {
        $data = array(
            array(
                'pagePublished'   => '1',
                'pageParent'      => '0',
                'pageTemplate'    => 'home',
                'pageBanner'      => '0',
                'pageURL'         => 'home',
                'enableJumbotron' => '1',
                'enableSlider'    => '0',
                'enableSearch'    => '1',
                'pageUpdated'     => date('Y-m-d H:i:s'),
            ),
            array(
                'pagePublished'   => '1',
                'pageParent'      => '0',
                'pageTemplate'    => 'page',
                'pageBanner'      => '0',
                'pageURL'         => 'contact',
                'enableJumbotron' => '1',
                'enableSlider'    => '0',
                'enableSearch'    => '0',
                'pageUpdated'     => date('Y-m-d H:i:s'),
            ),
            array(
                'pagePublished'   => '1',
                'pageParent'      => '0',
                'pageTemplate'    => 'news',
                'pageBanner'      => '0',
                'pageURL'         => 'blog',
                'enableJumbotron' => '0',
                'enableSlider'    => '0',
                'enableSearch'    => '1',
                'pageUpdated'     => date('Y-m-d H:i:s'),
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