<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hoosk_page_model extends CI_Model {
    public function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();

        $this->load->EloquentModel('Page_content_model');
        $this->load->EloquentModel('Page_meta_model');
        $this->load->EloquentModel('Page_attributes_model');
        $this->load->EloquentModel('Post_category');
        $this->load->EloquentModel('Post');
        $this->load->EloquentModel('Settings_model');
    }

    /*     * *************************** */
    /*     * ** Page Querys ************ */
    /*     * *************************** */

    public function getPage($pageURL) {
        // Get page
        $this->db->select("*");
        $this->db->join('hoosk_page_content', 'hoosk_page_content.pageID = hoosk_page_attributes.pageID');
        $this->db->join('hoosk_page_meta', 'hoosk_page_meta.pageID = hoosk_page_attributes.pageID');
        $this->db->where("pagePublished", 1);
        $this->db->where("pageURL", $pageURL);
        $query = $this->db->get('hoosk_page_attributes');
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $u):
                $page = array(
                    'pageID'          => $u['pageID'],
                    'pageTitle'       => $u['pageTitle'],
                    'pageKeywords'    => $u['pageKeywords'],
                    'pageDescription' => $u['pageDescription'],
                    'pageContentHTML' => $u['pageContentHTML'],
                    'pageTemplate'    => $u['pageTemplate'],
                    'enableJumbotron' => $u['enableJumbotron'],
                    'enableSlider'    => $u['enableSlider'],
                    'jumbotronHTML'   => $u['jumbotronHTML'],
                );
            endforeach;
            return $page;
        }
        return array('pageID' => "", 'pageTemplate' => "");

        /*
        $this->page_attributes_model
        ->leftJoin($this->page_content_model->getTable(),$this->page_content_model->getTable().'.pageID','=',$this->page_attributes_model->getTable().'.pageID')
        ->leftJoin($this->page_meta_model->getTable(),$this->page_meta_model->getTable().'.pageID','=',$this->page_attributes_model->getTable().'.pageID')
        ->where()
        /*
    }

    public function getCategory($catSlug) {
        // Get category
        $this->db->select("*");
        $this->db->where("categorySlug", $catSlug);
        $query = $this->db->get('hoosk_post_category');
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $u):
                $category = array(
                    'pageID'          => $u['categoryID'],
                    'categoryID'      => $u['categoryID'],
                    'pageTitle'       => $u['categoryTitle'],
                    'pageKeywords'    => '',
                    'pageDescription' => $u['categoryDescription'],
                );
            endforeach;
            return $category;
        }
        return array('categoryID' => "");
    }

    public function getArticle($postURL) {
        // Get article
        $this->db->select("*");
        $this->db->where("postURL", $postURL);
        $this->db->where("published", 1);
        $this->db->join('hoosk_post_category', 'hoosk_post_category.categoryID = hoosk_post.categoryID');
        $query = $this->db->get('hoosk_post');
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $u):
                $category = array(
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
            endforeach;
            return $category;
        }
        return array('postID' => "");
    }

    public function getSettings() {
        // Get settings
        $this->db->select("*");
        $this->db->where("siteID", 0);
        $query = $this->db->get('hoosk_settings');
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $u):
                $page = array(
                    'siteLogo'               => $u['siteLogo'],
                    'siteFavicon'            => $u['siteFavicon'],
                    'siteTitle'              => $u['siteTitle'],
                    'siteTheme'              => $u['siteTheme'],
                    'siteFooter'             => $u['siteFooter'],
                    'siteMaintenanceHeading' => $u['siteMaintenanceHeading'],
                    'siteMaintenanceMeta'    => $u['siteMaintenanceMeta'],
                    'siteMaintenanceContent' => $u['siteMaintenanceContent'],
                    'siteMaintenance'        => $u['siteMaintenance'],
                    'siteAdditionalJS'       => $u['siteAdditionalJS'],
                );
            endforeach;
            return $page;
        }
        return array();
    }
}
