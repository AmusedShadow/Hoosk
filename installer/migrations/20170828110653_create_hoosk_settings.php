<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_settings extends CI_Migration {
    protected $table = 'hoosk_settings';

    public function up() {
        Schema::create_table($this->table, function ($table) {
            $table->auto_increment_integer('settingID');
            $table->integer('siteID');
            $table->text('siteTitle');
            $table->text('siteDescription');
            $table->text('siteLogo');
            $table->text('siteFavicon');
            $table->string('siteTheme', 250);
            $table->text('siteFooter');
            $table->text('siteLang');
            $table->integer('siteMaintenance');
            $table->text('siteMaintenanceHeading');
            $table->text('siteMaintenanceMeta');
            $table->text('siteMaintenanceContent');
            $table->text('siteAdditionalJS');
        });

        $this->seed();
    }

    public function down() {
        $this->dbforge->drop_table($this->table);
    }

    protected function seed() {
        $this->db->insert($this->table, array(
            'siteID'                 => 0,
            'siteTitle'              => 'Hoosk Demo',
            'siteDescription'        => 'Hoosk',
            'siteLogo'               => 'logo.png',
            'siteFavicon'            => 'favicon.png',
            'siteTheme'              => 'dark',
            'siteFooter'             => '&copy; Hoosk CMS ' . date('Y'),
            'siteLang'               => 'english/',
            'siteMaintenance'        => 0,
            'siteMaintenanceHeading' => 'Down for maintenance',
            'siteMaintenanceMeta'    => 'Down for maintenance',
            'siteMaintenanceContent' => 'This site is currently down for maintenance, please check back soon.',
            'siteAdditionalJS'       => '',
        ));
    }
}