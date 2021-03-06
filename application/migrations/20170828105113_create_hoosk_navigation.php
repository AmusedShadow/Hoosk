<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_navigation extends CI_Migration {
    protected $table = 'hoosk_navigation';

    public function up() {
        Schema::create_table($this->table, function ($table) {
            $table->auto_increment_integer('navigationID');
            $table->string('navSlug', 10);
            $table->text('navTitle');
            $table->text('navHTML');
            $table->text('navEdit');
        });

        $this->seed();
    }

    public function down() {
        $this->dbforge->drop_table($this->table);
    }

    protected function seed() {
        $data = array(
            array(
                'navSlug'  => 'header',
                'navTitle' => 'Header Nav',
                'navHTML'  => '<ul class="nav navbar-nav"><li><a href="/index.php">Home</a></li><li><a href="/contact">Contact</a></li><li><a href="/blog">Blog</a></li></ul>',
                'navEdit'  => '',
            ),
            array(
                'navSlug'  => 'test',
                'navTitle' => 'test',
                'navHTML'  => '<ul class="nav navbar-nav"></ul>',
                'navEdit'  => '',
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