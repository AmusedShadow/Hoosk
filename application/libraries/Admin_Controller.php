<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends CI_Controller {
    protected $data         = array();
    protected $siteSettings = array();
    protected $adminUser    = array();

    public function __construct() {
        parent::__construct();

        //load the database up
        $this->load->database();

        //load some models
        $this->load->model('Hoosk_model');
        $this->load->EloquentModel('Settings_model');
        $this->load->EloquentModel('User_model');
        $this->load->EloquentModel('Post_model');
        $this->load->EloquentModel('Post_category_model');

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

        //get the currently logged in user
        $adm = $this->user_model->getUserByUsername($this->session->userdata('userName'));
        if (isset($adm[0])) {
            $this->adminUser = $adm[0];
        }
    }

    /**
     * _views
     * A helper method for loading views and such
     *
     * @access protected
     * @param  array   $views  - An array of views that should be loaded
     * @param  array   $data   - Additional data that should be sent to the views
     * @param  boolean $header [description]
     * @param  boolean $footer [description]
     */
    protected function _views($views = array(), $header = true, $footer = true) {
        //add the current user data to the data sent to the views
        $this->data['currentUser'] = $this->adminUser;

        //should we load the header?
        if ($header == true) {
            $this->data['header'] = $this->load->view('admin/header', $this->data, true);
        }

        //should we load the footer
        if ($footer == true) {
            $this->data['footer'] = $this->load->view('admin/footer', $this->data, true);
        }

        //loop through our views and load them
        foreach ($views as $view) {
            $this->load->view($view, $this->data);
        }
    }
}