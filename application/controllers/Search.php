<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {
    protected $term = '';
    protected $results = array();

    public function __construct() {
        parent::__construct();

        $this->load->helper('hoosk_page_helper');
        $this->load->model('hoosk_model');
        $this->load->model('Hoosk_page_model');
        $this->data['settings'] = $this->Hoosk_page_model->getSettings();
        define('SITE_NAME', $this->data['settings']['siteTitle']);
        define('THEME', $this->data['settings']['siteTheme']);
        define('THEME_FOLDER', BASE_URL . '/theme/' . THEME);

        $this->load->library('parser');
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

        $this->data['header'] = $this->load->view('templates/header', $this->data, true);
        $this->data['footer'] = $this->load->view('templates/footer', $this->data, true);
        $this->data['results'] = $this->results;
        $this->parser->parse('templates/search_results', $this->data);
    }

    protected function _post() {
        $this->term = trim($this->input->post('term'));
        $this->_runSearch();
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

        $test = array();
        $all = $all->reject(function($data) use (&$test) {
            if (!isset($test[$data['type']])) {
                $test[$data['type']] = array();
            }

            if (in_array($data['id'],$test[$data['type']])) {
                return true;
            }

            $test[$data['type']][] = $data['id'];
        });

        $results = array();
        foreach ($all as $result) {
            switch ($result['type']) {
                case 'page_content':
                    $pageData = $this->page_content_model->getPage($result['id']);
                    if ($pageData['pagePublished']==1) {
                        $results[] = array(
                            'type' => 'Page',
                            'title' => $pageData['pageTitle'],
                            'url' => $pageData['pageURL']
                        );
                    }
                break;

                case 'page_meta':
                    $metaData = $this->page_meta_model->getMeta($result['id']);
                    if ($metaData['pagePublished']==1) {
                        $results[] = array(
                            'type' => 'Page',
                            'title' => $metaData['pageTitle'],
                            'url' => $metaData['pageURL']
                        );
                    }
                break;

                case 'post':
                    $postData = $this->post_model->getPost($result['id']);
                    if ($postData['pagePublished']==1) {
                        $results[] = array(
                            'type' => 'Blog Post',
                            'title' => $postData['postTitle'],
                            'url' => $postData['postURL']
                        );
                    }
                break;

                case 'post_category':
                    $categoryData = $this->post_category_model->getCategory($result['id']);
                    if ($categoryData['counter']>0) {
                        $results[] = array(
                            'type' => 'Blog Category',
                            'title' => $categoryData['categoryTitle'],
                            'url' => 'category/'.$categoryData['categorySlug']
                        );
                    }
                break;
            }
        }

        $this->results = $results;
    }

    public function _remap($method, $params = array()) {
        $this->index();
    }
}
