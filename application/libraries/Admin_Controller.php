<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends CI_Controller {
    protected $data         = array();
    protected $siteSettings = array();

    public function __construct() {
        parent::__construct();

        //load some models
        $this->load->model('Hoosk_model');
        $this->load->EloquentModel('Settings_model');
        $this->load->EloquentModel('User_model');

        //load some helpers
        $this->load->helper('admincontrol');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('hoosk_admin');
        $this->load->helper('file');

        //load some libraries
        $this->load->library('session'); //why is this loaded via library? I thought it was a driver?
        $this->load->library('form_validation');

        //start out view data
        $this->data['current'] = $this->uri->segment(2); //Define what page we are on for nav

        //get our site settings
        $this->siteSettings = $this->settings_model->getSiteSettings();

        //due to a bug we need to rtrim the theme and lang
        if (isset($this->siteSettings['siteLang'])) {
            $this->siteSettings['siteLang'] = trim($this->siteSettings['siteLang'], '\\');
        }

        if (isset($this->siteSettings['siteTheme'])) {
            $this->siteSettings['siteTheme'] = trim($this->siteSettings['siteTheme'], '\\');
        }

        define("HOOSK_ADMIN", 1);
        define('LANG', $this->siteSettings['siteLang']);
        define('SITE_NAME', $this->siteSettings['siteTitle']);
        define('THEME', $this->siteSettings['siteTheme']);
        define('THEME_FOLDER', BASE_URL . '/theme/' . THEME);

        //load the language file
        $this->lang->load('admin', LANG);
    }
}