<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_social extends CI_Migration {
    protected $table = 'hoosk_social';

    public function up() {
        Schema::create_table($this->table, function ($table) {
            $table->auto_increment_integer('socialID');
            $table->string('socialName', 250);
            $table->string('socialLink', 250);
            $table->integer('socialEnabled');
        });

        $this->seed();
    }

    public function down() {
        $this->dbforge->drop_table($this->table);
    }

    protected function seed() {
        $networks = array(
            'twitter',
            'facebook',
            'google',
            'pinterest',
            'foursquare',
            'linkedin',
            'myspace',
            'soundcloud',
            'spotify',
            'lastfm',
            'youtube',
            'vimeo',
            'dailymotion',
            'vine',
            'flickr',
            'instagram',
            'tumblr',
            'reddit',
            'envato',
            'github',
            'tripadvisor',
            'stackoverflow',
            'persona',
        );

        foreach ($networks as $social) {
            $query = $this->db->select('socialID')->from($this->table)->where('socialName', $social)->limit(0, 1)->get();
            if ($query->num_rows() == 0) {
                $this->db->insert($this->table, array(
                    'socialName'    => $social,
                    'socialLink'    => '',
                    'socialEnabled' => 0,
                ));
            }
        }
    }
}