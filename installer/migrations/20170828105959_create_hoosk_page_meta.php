<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_page_meta extends CI_Migration {
    protected $table = 'hoosk_page_meta';

    public function up() {
        Schema::create_table($this->table, function ($table) {
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
        $data = array(
            array(
                'pageID'          => 1,
                'pageKeywords'    => 'Hoosk Keywords',
                'pageDescription' => 'Hoosk Description',
            ),
            array(
                'pageID'          => 2,
                'pageKeywords'    => 'Contact',
                'pageDescription' => 'Contact',
            ),
            array(
                'pageID'          => 3,
                'pageKeywords'    => 'test',
                'pageDescription' => 'test',
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