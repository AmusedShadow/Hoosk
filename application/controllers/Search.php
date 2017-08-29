<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {
    protected $term = '';

    public function __construct() {
        parent::__construct();

        $this->load->model('hoosk_model');
        $this->load->model('Hoosk_page_model');
        $this->data['settings'] = $this->Hoosk_page_model->getSettings();
        define('SITE_NAME', $this->data['settings']['siteTitle']);
        define('THEME', $this->data['settings']['siteTheme']);
        define('THEME_FOLDER', BASE_URL . '/theme/' . THEME);
    }

    public function index() {
        $method = strtolower(trim($this->input->method()));
        switch ($method) {
        case 'post':
            $this->_post();
            break;

        default:
            $this->_get();
            break;
        }
    }

    protected function _post() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('term', 'Search Term', 'required');
        if ($this->form_validation->run() == false) {

        } else {

        }
    }

    protected function _get() {
        $term       = str_replace('search/term/', '', $this->uri->uri_string());
        $term       = urldecode($term);
        $this->term = $term;

        $this->_runSearch();
    }

    protected function _runSearch() {
        $results[] = $this->navigation_model->search($this->term);
        $results[] = $this->page_attributes_model->search($this->term);
        $results[] = $this->page_content_model->search($this->term);
        $results[] = $this->page_meta_model->search($this->term);
        $results[] = $this->post_model->search($this->term);
        $results[] = $this->post_category_model->search($this->term);

        $all = collect();
        foreach ($results as $result) {
            $all = $all->merge($result);
        }

        echo '<pre>';
        print_r($all);
        exit;
    }

    public function _remap($method, $params = array()) {
        $this->index();
    }
}
