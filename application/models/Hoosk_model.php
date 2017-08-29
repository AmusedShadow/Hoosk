<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hoosk_model extends CI_Model {
    protected $settings = array();

    public function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();

        $this->load->EloquentModel('Settings_model');
        $this->load->EloquentModel('Page_content_model');
        $this->load->EloquentModel('Page_attributes_model');
        $this->load->EloquentModel('Page_meta_model');
        $this->load->EloquentModel('User_model');
        $this->load->EloquentModel('Social_model');
        $this->load->EloquentModel('Banner_model');
        $this->load->EloquentModel('Navigation_model');
        $this->load->EloquentModel('Post_model');
        $this->load->EloquentModel('Post_category_model');

        $this->settings = $this->settings_model->where('siteID', '=', 0)->first()->toArray();
    }

    /*     * *************************** */
    /*     * ** Dash Querys ************ */
    /*     * *************************** */
    public function getSiteName() {
        return (isset($this->settings['siteTitle'])) ? $this->settings['siteTitle'] : array();
    }

    public function checkMaintenance() {
        return (isset($this->settings['siteMaintenance'])) ? $this->settings['siteMaintenance'] : array();
    }

    public function getTheme() {
        return (isset($this->settings['siteTheme'])) ? $this->settings['siteTheme'] : array();
    }

    public function getLang() {
        return (isset($this->settings['siteLang'])) ? $this->settings['siteLang'] : array();
    }

    public function getUpdatedPages() {
        $query = $this->page_attributes_model
            ->select($this->page_content_model->getTable() . '.pageTitle', $this->page_content_model->getTable() . '.pageID', $this->page_attributes_model->getTable() . '.pageUpdated', $this->page_content_model->getTable() . '.pageContentHTML')
            ->leftJoin($this->page_content_model->getTable(), $this->page_content_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->leftJoin($this->page_meta_model->getTable(), $this->page_meta_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->orderBy($this->page_attributes_model->getTable() . '.pageUpdated', 'desc')
            ->take(5)
            ->get();

        if (count($query) == 0) {
            return array();
        }

        $return = array();
        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;
    }

    /*     * *************************** */
    /*     * ** User Querys ************ */
    /*     * *************************** */
    public function countUsers() {
        return $this->user_model->count();
    }

    public function getUsers($limit, $offset = 0) {
        $query = $this->user_model->select('userName', 'email', 'userID')
            ->orderBy('userName', 'asc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        if (count($query) == 0) {
            return array();
        }

        $return = array();
        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;
    }

    public function getUser($id) {
        $query = $this->user_model->where('userID', '=', $id)
            ->first();

        if (count($query) == 0) {
            return array();
        }

        return $query->toArray();
    }

    public function getUserEmail($id) {
        $query = $this->user_model->select('email')->where('userID', '=', $id)
            ->first();

        if (count($query) == 0) {
            return '';
        } else {
            return trim($query->email);
        }
    }

    public function createUser($username = '', $email = '', $password = '') {
        if (empty($username)) {
            $username = $this->input->post('username');
        }

        if (empty($email)) {
            $email = $this->input->post('email');
        }

        if (empty($password)) {
            $password = $this->input->post('password');
        }

        $this->user_model->insert(array(
            // Create the user account
            'userName' => $username,
            'email'    => $email,
            'password' => md5($password . SALT),
            'RS'       => '',

        ));
    }

    public function updateUser($id, $email = '', $password = '') {
        if (empty($email)) {
            $email = $this->input->post('email');
        }

        if (empty($password)) {
            $password = $this->input->post('password');
        }

        $password = md5($password . SALT);

        $this->user_model
            ->where('userID', '=', $id)
            ->update(array(
                // update the user account
                'email'    => $email,
                'password' => $password,
            ));
    }

    public function removeUser($id) {
        // Delete a user account
        $this->user_model->where('userID', '=', $id)->delete();
    }

    public function login($username, $password) {
        $query = $this->user_model->where('username', '=', $username)
            ->where('password', '=', $password)
            ->first();

        if (count($query) == 0) {
            return false;
        }

        $this->session->set_userdata(array(
            'userID'    => $query->userID,
            'userName'  => $query->userName,
            'logged_in' => true,
        ));
        return true;
    }

    /*     * *************************** */
    /*     * ** Page Querys ************ */
    /*     * *************************** */
    public function pageSearch($term) {
        $query = $this->page_attributes_model->leftJoin($this->page_content_model->getTable(), $this->page_content_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->leftJoin($this->page_meta_model->getTable(), $this->page_meta_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->where($this->page_content_model->getTable() . '.pageTitle', 'LIKE', $term);

        if (isset($limit)) {
            $query->limit($limit);
        }

        if (isset($offset)) {
            $query->offset($offset);
        }

        if (count($query) == 0) {
            echo "<tr><td colspan='5'><p>" . $this->lang->line('no_results') . "</p></td></tr>";
        } else {
            foreach ($query as $p) {
                $p = $p->toArray();

                echo '<tr>';
                echo '<td>' . $p['navTitle'] . '</td>';
                echo '<td>' . $p['pageUpdated'] . '</td>';
                echo '<td>' . $p['pageCreated'] . '</td>';
                echo '<td>' . ($p['pagePublished'] ? '<span class="fa fa-2x fa-check-circle"></span>' : '<span class="fa fa-2x fa-times-circle"></span>') . '</td>';
                echo '<td class="td-actions"><a href="' . BASE_URL . '/admin/pages/jumbo/' . $p['pageID'] . '" class="btn btn-small btn-primary">' . $this->lang->line('btn_jumbotron') . '</a> <a href="' . BASE_URL . '/admin/pages/edit/' . $p['pageID'] . '" class="btn btn-small btn-success"><i class="fa fa-pencil"> </i></a> <a data-toggle="modal" data-target="#ajaxModal" class="btn btn-danger btn-small" href="' . BASE_URL . '/admin/pages/delete/' . $p['pageID'] . '"><i class="fa fa-remove"> </i></a></td>';
                echo '</tr>';
            }
        }
    }

    public function countPages() {
        return $this->page_attributes_model->count();
    }

    public function getPages($limit = 0, $offset = 0) {
        $query = $this->page_attributes_model->leftJoin($this->page_content_model->getTable(), $this->page_content_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->leftJoin($this->page_meta_model->getTable(), $this->page_meta_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID');
        if ($limit > 0) {
            $query->limit($limit);
        }

        if ($offset > 0) {
            $query->offset($offset);
        }

        $query = $query->get();

        if (count($query) == 0) {
            return array();
        }

        $return = array();
        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;
    }

    public function getPagesAll() {
        return $this->getPages(0, 0);
    }

    public function createPage($published = null, $template = null, $url = null, $parent = null, $banner = null, $jumbo = null, $slider = null, $content = null) {
        if (is_null($published)) {
            $published = $this->input->post('pagePublished');
        }

        if (is_null($template)) {
            $template = $this->input->post('pageTemplate');
        }

        if (is_null($url)) {
            $url = $this->input->post('pageURL');
        }

        if (is_null($parent)) {
            $parent = 0;
        }

        if (is_null($banner)) {
            $banner = 0;
        }

        if (is_null($jumbo)) {
            $jumbo = 0;
        }

        if (is_null($slider)) {
            $slider = 0;
        }

        if (is_null($content)) {
            $content = $this->input->post('content');
        }

        if (!empty($content)) {
            $converter   = new Converter();
            $HTMLContent = $converter->toHtml($content);
        } else {
            $HTMLContent = '';
        }

        $m                  = $this->page_attributes_model->newInstance();
        $m->pagePublished   = $published;
        $m->pageTemplate    = $template;
        $m->pageURL         = $url;
        $m->pageParent      = $parent;
        $m->pageBanner      = $banner;
        $m->enableJumbotron = $jumbo;
        $m->enableSlider    = $slider;
        $m->enableSearch    = 0;
        $m->save();

        $this->page_content_model->insert(array(
            'pageID'          => $m->pageID,
            'pageTitle'       => $this->input->post('pageTitle'),
            'navTitle'        => $this->input->post('navTitle'),
            'pageContent'     => $this->input->post('content'),
            'pageContentHTML' => $HTMLContent,
            'jumbotron'       => '',
            'jumbotronHTML'   => '',
        ));

        $this->page_meta_model->insert(array(
            'pageID'          => $m->pageID,
            'pageKeywords'    => $this->input->post('pageKeywords'),
            'pageDescription' => $this->input->post('pageDescription'),
        ));
    }

    public function getPage($id) {
        $query = $this->page_attributes_model
            ->leftJoin($this->page_content_model->getTable(), $this->page_content_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->leftJoin($this->page_meta_model->getTable(), $this->page_meta_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->where($this->page_attributes_model->getTable() . '.pageID', '=', $id)
            ->get();

        if (count($query) == 0) {
            return array();
        }

        $return = array();
        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;

    }

    public function getPageBanners($id) {
        $query = $this->banner_model
            ->where('pageID', '=', $id)
            ->orderBy('slideOrder', 'ASC')
            ->get();

        $return = array();
        if (count($query) > 0) {
            foreach ($query as $row) {
                $return[] = $row->toArray();
            }
        }

        return $return;
    }

    public function removePage($id) {
        $this->page_content_model->where('pageID', '=', $id)->delete();
        $this->page_meta_model->where('pageID', '=', $id)->delete();
        $this->page_attributes_model->where('pageID', '=', $id)->delete();
    }

    public function getPageURL($id) {
        $query = $this->page_attributes_model->where('pageID', '=', $id)->first();

        if (count($query) == 0) {
            return '';
        }

        return $query->pageURL;
    }

    public function updatePage($id) {
        // Update the page

        if ($this->input->post('content') != "") {
            $sirTrevorInput = $this->input->post('content');
            $converter      = new Converter();
            $HTMLContent    = $converter->toHtml($sirTrevorInput);
        } else {
            $HTMLContent = "";
        }

        if ($id != 1) {
            $data = array(
                'pagePublished' => $this->input->post('pagePublished'),
                'pageURL'       => $this->input->post('pageURL'),
                'pageTemplate'  => $this->input->post('pageTemplate'),
            );
        } else {
            $data = array(
                'pagePublished' => $this->input->post('pagePublished'),
                'pageTemplate'  => $this->input->post('pageTemplate'),
            );
        }
        $this->db->where("pageID", $id);
        $this->db->update('hoosk_page_attributes', $data);
        $contentdata = array(
            'pageTitle'       => $this->input->post('pageTitle'),
            'navTitle'        => $this->input->post('navTitle'),
            'pageContent'     => $this->input->post('content'),
            'pageContentHTML' => $HTMLContent,
        );
        $this->db->where("pageID", $id);
        $this->db->update('hoosk_page_content', $contentdata);
        $metadata = array(
            'pageKeywords'    => $this->input->post('pageKeywords'),
            'pageDescription' => $this->input->post('pageDescription'),
        );
        $this->db->where("pageID", $id);
        $this->db->update('hoosk_page_meta', $metadata);
    }

    public function updateJumbotron($id) {
        // Update the jumbotron
        if ($this->input->post('jumbotron') != "") {
            $sirTrevorInput = $this->input->post('jumbotron');
            $converter      = new Converter();
            $HTMLContent    = $converter->toHtml($sirTrevorInput);
        } else {
            $HTMLContent = "";
        }
        $data = array(
            'enableJumbotron' => $this->input->post('enableJumbotron'),
            'enableSlider'    => $this->input->post('enableSlider'),
        );

        $this->db->where("pageID", $id);
        $this->db->update('hoosk_page_attributes', $data);
        $contentdata = array(
            'jumbotron'     => $this->input->post('jumbotron'),
            'jumbotronHTML' => $HTMLContent,
        );
        $this->db->where("pageID", $id);
        $this->db->update('hoosk_page_content', $contentdata);

        // Clear the sliders
        $this->db->delete('hoosk_banner', array('pageID' => $id));

        $sliders = explode('{', $this->input->post('pics'));

        for ($i = 1; $i < count($sliders); $i++) {
            $div = explode('|', $sliders[$i]);

            $slidedata = array(
                'pageID'     => $id,
                'slideImage' => $div[0],
                'slideLink'  => $div[1],
                'slideAlt'   => substr($div[2], 0, -1),
                'slideOrder' => $i - 1,
            );

            $this->db->insert('hoosk_banner', $slidedata);
        }
    }

    /*     * *************************** */
    /*     * ** Navigation Querys ****** */
    /*     * *************************** */
    public function countNavigation() {
        return $this->navigation_model->count();
    }

    public function getAllNav($limit = 0, $offset = 0) {
        $query = $this->navigation_model->select('*');
        if ($limit > 0) {
            $query->limit($limit);
        }

        if ($offset > 0) {
            $query->offset($offset);
        }

        $query = $query->get();

        if (count($query) == 0) {
            return array();
        }

        $return = array();
        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;
    }

    public function getNav($id) {
        $return = array();
        $query  = $this->navigation_model->where('navSlug', '=', $id)
            ->get();

        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;
    }

    //Get page details for building nav
    public function getPageNav($url) {
        $query = $this->page_attributes_model
            ->leftJoin($this->page_content_model->getTable(), $this->page_content_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->leftJoin($this->page_meta_model->getTable(), $this->page_meta_model->getTable() . '.pageID', '=', $this->page_attributes_model->getTable() . '.pageID')
            ->where($this->page_attributes_model->getTable() . '.pageURL', '=', $url)
            ->first();

        $return = array();
        if (count($query) > 0) {
            $return[] = $query->toArray();
        }

        return $return;
    }

    public function insertNav($nav = null, $serial = null) {
        if (is_null($nav)) {
            $nav = $this->input->post('convertedNav');
        }

        if (is_null($serial)) {
            $serial = $this->input->post('serialNav');
        }

        $navigationHTML = $nav;
        $navigationHTML = str_replace("<ul></ul>", "", $navigationHTML);
        $navigationEdit = $serial;
        $navigationEdit = str_replace('<button data-action="collapse" type="button">Collapse</button><button style="display: none;" data-action="expand" type="button">Expand</button>', "", $navigationEdit);

        $data = array(
            'navSlug'  => $this->input->post('navSlug'),
            'navTitle' => $this->input->post('navTitle'),
            'navEdit'  => $navigationEdit,
            'navHTML'  => $navigationHTML,
        );

        $this->navigation_model->insert($data);
    }

    public function updateNav($id, $nav = null, $serial = null) {
        if (is_null($nav)) {
            $nav = $this->input->post('convertedNav');
        }

        if (is_null($serial)) {
            $serial = $this->input->post('seriaNav');
        }

        $navigationHTML = $nav;
        $navigationHTML = str_replace("<ul></ul>", "", $navigationHTML);
        $navigationEdit = $serial;
        $navigationEdit = str_replace('<button data-action="collapse" type="button">Collapse</button><button style="display: none;" data-action="expand" type="button">Expand</button>', "", $navigationEdit);

        $data = array(
            'navTitle' => $this->input->post('navTitle'),
            'navEdit'  => $navigationEdit,
            'navHTML'  => $navigationHTML,
        );
        $this->navigation_model->where('navSlug', '=', $id)->update($data);
    }

    public function removeNav($id) {
        // Delete a nav
        $this->navigation_model->where('navSlug', '=', $id)->delete();
    }

    public function getSettings() {
        // Get the settings
        $return[] = $this->settings;
        return $return;
    }

    public function updateSettings() {
        $data = array(
            'siteTheme'              => $this->input->post('siteTheme'),
            'siteLang'               => $this->input->post('siteLang'),
            'siteFooter'             => $this->input->post('siteFooter'),
            'siteMaintenance'        => $this->input->post('siteMaintenance'),
            'siteMaintenanceHeading' => $this->input->post('siteMaintenanceHeading'),
            'siteMaintenanceMeta'    => $this->input->post('siteMaintenanceMeta'),
            'siteMaintenanceContent' => $this->input->post('siteMaintenanceContent'),
            'siteAdditionalJS'       => $this->input->post('siteAdditionalJS'),
        );

        if ($this->input->post('siteTitle') != "") {
            $data['siteTitle'] = $this->input->post('siteTitle');
        }

        if ($this->input->post('siteLogo') != "") {
            $data['siteLogo'] = $this->input->post('siteLogo');
        }
        if ($this->input->post('siteFavicon') != "") {
            $data['siteFavicon'] = $this->input->post('siteFavicon');
        }

        $this->settings_model->where('siteID', '=', 0)->update($data);
    }

    /*     * *************************** */
    /*     * ** Post Querys ************ */
    /*     * *************************** */
    public function postSearch($term) {
        $query = $this->post_model
            ->leftJoin($this->post_category_model->getTable(), $this->post_category_model->getTable() . '.categoryID', '=', $this->post_model->getTable() . '.categoryID')
            ->where($this->post_model->getTable() . '.postTitle', 'LIKE', $term)
            ->orderBy('unixStamp', 'DESC');

        if (empty($term)) {
            $query->limit(15);
        }

        $query = $query->get();
        if (count($query) == 0) {
            echo "<tr><td colspan='5'><p>" . $this->lang->line('no_results') . "</p></td></tr>";
        } else {
            foreach ($query as $row) {
                $p = $row->toArray();
                echo '<tr>';
                echo '<td>' . $p['postTitle'] . '</td>';
                echo '<td>' . $p['categoryTitle'] . '</td>';
                echo '<td>' . $p['datePosted'] . '</td>';
                echo '<td>' . ($p['published'] ? '<span class="fa fa-2x fa-check-circle"></span>' : '<span class="fa fa-2x fa-times-circle"></span>') . '</td>';
                echo '<td class="td-actions"><a href="' . BASE_URL . '/admin/posts/edit/' . $p['postID'] . '" class="btn btn-small btn-success"><i class="fa fa-pencil"> </i></a> <a data-toggle="modal" data-target="#ajaxModal" class="btn btn-danger btn-small" href="' . BASE_URL . '/admin/posts/delete/' . $p['postID'] . '"><i class="fa fa-remove"> </i></a></td>';
                echo '</tr>';
            }
        }
    }

    public function countPosts() {
        return $this->post_model->count();
    }

    public function getPosts($limit = 0, $offset = 0) {
        $query = $this->post_model
            ->leftJoin($this->post_category_model->getTable(), $this->post_category_model->getTable() . '.categoryID', '=', $this->post_model->getTable() . '.categoryID')
            ->orderBy('unixStamp', 'DESC');

        if ($limit > 0) {
            $query->limit($limit);
        }

        if ($offset > 0) {
            $query->offset($offset);
        }

        $query = $query->get();

        $return = array();
        if (count($query) > 0) {
            foreach ($query as $row) {
                $return[] = $row->toArray();
            }
        }

        return $return;
    }

    public function createPost() {
        // Create the post
        if ($this->input->post('content') != "") {
            $sirTrevorInput = $this->input->post('content');
            $converter      = new Converter();
            $HTMLContent    = $converter->toHtml($sirTrevorInput);
        } else {
            $HTMLContent = "";
        }
        $data = array(
            'postTitle'       => $this->input->post('postTitle'),
            'categoryID'      => $this->input->post('categoryID'),
            'postURL'         => $this->input->post('postURL'),
            'postContent'     => $this->input->post('content'),
            'postContentHTML' => $HTMLContent,
            'postExcerpt'     => $this->input->post('postExcerpt'),
            'published'       => $this->input->post('published'),
            'datePosted'      => $this->input->post('datePosted'),
            'unixStamp'       => $this->input->post('unixStamp'),
            'postImage'       => '',
        );
        if ($this->input->post('postImage') != "") {
            $data['postImage'] = $this->input->post('postImage');
        }
        $this->post_model->insert($data);
    }

    public function getPost($id) {
        $query = $this->post_model
            ->leftJoin($this->post_category_model->getTable(), $this->post_category_model->getTable() . '.categoryID', '=', $this->post_model->getTable() . '.categoryID')
            ->where($this->post_model->getTable() . '.postID', '=', $id)
            ->first();

        $return = array();
        if (count($query) > 0) {
            $return = $query->toArray();
        }

        return $return;
    }

    public function removePost($id) {
        // Delete a post
        $this->post_model->where('postID', '=', $id)->delete();
    }

    public function updatePost($id) {
        // Update the post

        if ($this->input->post('content') != "") {
            $sirTrevorInput = $this->input->post('content');
            $converter      = new Converter();
            $HTMLContent    = $converter->toHtml($sirTrevorInput);
        } else {
            $HTMLContent = "";
        }
        $data = array(
            'postTitle'       => $this->input->post('postTitle'),
            'categoryID'      => $this->input->post('categoryID'),
            'postURL'         => $this->input->post('postURL'),
            'postContent'     => $this->input->post('content'),
            'postContentHTML' => $HTMLContent,
            'postExcerpt'     => $this->input->post('postExcerpt'),
            'published'       => $this->input->post('published'),
            'datePosted'      => $this->input->post('datePosted'),
            'unixStamp'       => $this->input->post('unixStamp'),
        );
        if ($this->input->post('postImage') != "") {
            $data['postImage'] = $this->input->post('postImage');
        }
        $this->post_model->where('postID', '=', $id)->update($data);
    }

    /*     * *************************** */
    /*     * ** Category Querys ******** */
    /*     * *************************** */
    public function countCategories() {
        return $this->post_category_model->count();
    }

    public function getCategories() {
        return $this->getCategoriesAll(0, 0);
    }

    public function getCategoriesAll($limit = 0, $offset = 0) {
        $query = $this->post_category_model;
        if ($limit > 0) {
            $query->limit($limit);
        }

        if ($offset > 0) {
            $query->offset($offset);
        }

        $query = $query->get();
        if (count($query) == 0) {
            return array();
        }

        $return = array();
        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;
    }

    public function createCategory($title = '', $slug = '', $desc = '') {
        if (empty($title)) {
            $title = $this->input->post('categoryTitle');
        }

        if (empty($slug)) {
            $slug = $this->input->post('categorySlug');
        }

        if (empty($desc)) {
            $desc = $this->input->post('categoryDescription');
        }

        $this->post_category_model->insert(array(
            'categoryTitle'       => $title,
            'categorySlug'        => $slug,
            'categoryDescription' => $desc,
        ));
    }

    public function getCategory($id) {
        $query = $this->post_category_model->where('categoryID', '=', $id)->first();

        if (count($query) == 0) {
            return array();
        }

        return $query->toArray();
    }

    public function removeCategory($id) {
        // Delete a category
        $this->post_category_model->where('categoryID', '=', $id)->delete();
    }

    public function updateCategory($id, $title = '', $slug = '', $desc = '') {
        if (empty($title)) {
            $title = $this->input->post('categoryTitle');
        }

        if (empty($slug)) {
            $slug = $this->input->post('categorySlug');
        }

        if (empty($desc)) {
            $desc = $this->input->post('categoryDescription');
        }

        $this->post_category_model->where('categoryID', '=', $id)->update(array(
            'categoryTitle'       => $title,
            'categorySlug'        => $slug,
            'categoryDescription' => $desc,
        ));
    }

    /*     * *************************** */
    /*     * ** Social Querys ********** */
    /*     * *************************** */

    public function getSocial() {
        $query = $this->social_model->get();
        if (count($query) == 0) {
            return array();
        }

        $return = array();
        foreach ($query as $row) {
            $return[] = $row->toArray();
        }

        return $return;
    }

    public function updateSocial() {
        foreach ($this->getSocial() as $social) {
            $link    = $this->input->post($social['socialName']);
            $enabled = $this->input->post('checkbox' . $social['socialName']);

            if (is_null($link)) {
                continue;
            }

            $this->social_model->where('socialName', '=', $social['socialName'])->update(array(
                'socialLink'    => $link,
                'socialEnabled' => (!is_null($enabled)) ? $enabled : 0,
            ));
        }
    }
}
