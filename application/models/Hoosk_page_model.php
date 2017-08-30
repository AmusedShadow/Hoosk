<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hoosk_page_model extends CI_Model {
    public function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();

        $this->load->EloquentModel('Page_content_model');
        $this->load->EloquentModel('Page_meta_model');
        $this->load->EloquentModel('Page_attributes_model');
        $this->load->EloquentModel('Post_category_model');
        $this->load->EloquentModel('Post_model');
        $this->load->EloquentModel('Settings_model');
    }

    /*     * *************************** */
    /*     * ** Page Querys ************ */
    /*     * *************************** */

    public function getPage($pageURL) {
        $query = $this->page_attributes_model
            ->leftJoin($this->page_content_model->getTable(), $this->page_content_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->leftJoin($this->page_meta_model->getTable(), $this->page_meta_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->where($this->page_attributes_model->getTable() . '.pagePublished', '=', 1)
            ->where($this->page_attributes_model->getTable() . '.pageURL', '=', $pageURL)
            ->first();

        $return = array('pageID' => '', 'pageTemplate' => '');
        if (count($query) > 0) {
            $return = $query->toArray();
        }

        return $return;
    }

    public function getCategory($catSlug) {
        $query = $this->post_category_model->where('categorySlug', '=', $catSlug)
            ->first();

        $return = array('categoryID' => '');
        if (count($query) > 0) {
            $u = $query->toArray();

            $return = array(
                'pageID'          => $u['categoryID'],
                'categoryID'      => $u['categoryID'],
                'pageTitle'       => $u['categoryTitle'],
                'pageKeywords'    => '',
                'pageDescription' => $u['categoryDescription'],
            );
        }

        return $return;
    }

    public function getArticle($postURL) {
        $query = $this->post_model
            ->leftJoin($this->post_category_model->getTable(), $this->post_category_model->getTable() . '.categoryID', '=', $this->post_model->getTable() . '.categoryID')
            ->where($this->post_model->getTable() . '.postURL', '=', $postURL)
            ->where($this->post_model->getTable() . '.published', '=', 1)
            ->first();

        $return = array('postID' => '');
        if (count($query) > 0) {
            $u = $query->toArray();

            $return = array(
                'pageID'          => $u['postID'],
                'postID'          => $u['postID'],
                'pageTitle'       => $u['postTitle'],
                'pageKeywords'    => '',
                'pageDescription' => $u['postExcerpt'],
                'postContent'     => $u['postContentHTML'],
                'datePosted'      => $u['datePosted'],
                'categoryTitle'   => $u['categoryTitle'],
                'categorySlug'    => $u['categorySlug'],
            );
        }

        return $return;
    }

    public function getSettings() {
        $query = $this->settings_model->where('siteID', '=', 0)->first();

        $return = array();
        if (count($query) > 0) {
            $return = $query->toArray();
        }

        return $return;
    }
}
