<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller {
    public function index() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));

        $this->data['current']         = $this->uri->segment(2);
        $this->data['recenltyUpdated'] = $this->Hoosk_model->getUpdatedPages();
        if (RSS_FEED) {
            $this->load->library('rssparser');
            $this->rssparser->set_feed_url('http://hoosk.org/feed/rss');
            $this->rssparser->set_cache_life(30);
            $this->data['hooskFeed'] = $this->rssparser->getFeed(3);
        }

        $this->data['maintenaceActive'] = $this->Hoosk_model->checkMaintenance();

        $this->_views(array('admin/home'));
    }

    public function upload() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));
        $attachment   = $this->input->post('attachment');
        $uploadedFile = $_FILES['attachment']['tmp_name']['file'];

        $path = $_SERVER["DOCUMENT_ROOT"] . '/images';
        $url  = BASE_URL . '/images';

        // create an image name
        $fileName = $attachment['name'];

        // upload the image
        move_uploaded_file($uploadedFile, $path . '/' . $fileName);

        $this->output->set_output(
            json_encode(array('file' => array(
                'url'      => $url . '/' . $fileName,
                'filename' => $fileName,
            ))),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    public function login() {
        $this->data['header'] = $this->load->view('admin/headerlog', '', true);
        $this->_views(array('admin/login'), false, false);
    }

    public function loginCheck() {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password') . SALT);
        if ($this->Hoosk_model->login($username, $password) == true) {
            redirect(site_url('/admin'));
        } else {
            $this->data['error'] = "1";
            $this->login();
        }
    }

    public function ajaxLogin() {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password') . SALT);
        $result   = $this->Hoosk_model->login($username, $password);
        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function logout() {
        $data = array(
            'userID'    => '',
            'userName'  => '',
            'logged_in' => false,
        );
        $this->session->unset_userdata($data);
        $this->session->sess_destroy();
        $this->login();
    }

    public function settings() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));

        $this->form_validation->set_rules('siteTitle', $this->lang->line('settings_name'), 'required');
        $this->form_validation->set_rules('siteFooter', $this->lang->line('settings_footer'), 'required');
        $this->form_validation->set_rules('siteTheme', $this->lang->line('setting_theme'), 'required');
        $this->form_validation->set_rules('siteLang', $this->lang->line('setting_lang'), 'required');
        //$this->form_validation->set_rules('file_upload',$this->lang->line('settings_logo'),'trim');
        //$this->form_validation->set_rules('favicon_upload',$this->lang->line('settings_favicon'),'trim');
        $this->form_validation->set_rules('siteMaintenance', $this->lang->line('settings_maintenance'), 'required');
        $this->form_validation->set_rules('siteMaintenanceHeading', $this->lang->line('settings_maintenance_heading'), 'trim');
        $this->form_validation->set_rules('siteMaintenanceMeta', $this->lang->line('settings_maintenance_meta'), 'trim');
        $this->form_validation->set_rules('siteMaintenanceContent', $this->lang->line('settings_maintenance_content'), 'trim');
        $this->form_validation->set_rules('siteAdditionalJS', $this->lang->line('settings_additional_js'), 'trim');

        if ($this->form_validation->run() === false) {
            $this->load->helper('directory');
            $this->data['themesdir'] = directory_map($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'theme' . DIRECTORY_SEPARATOR, 1);
            $this->data['langdir']   = directory_map(APPPATH . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR, 1);

            $this->data['settings'] = $this->Hoosk_model->getSettings();
            $this->_views(array('admin/settings'));
        } else {
            $path_upload = $_SERVER["DOCUMENT_ROOT"] . '/uploads/';
            $path_images = $_SERVER["DOCUMENT_ROOT"] . '/images/';
            if ($this->input->post('siteLogo') != "") {
                rename($path_upload . $this->input->post('siteLogo'), $path_images . $this->input->post('siteLogo'));
            }
            if ($this->input->post('siteFavicon') != "") {
                rename($path_upload . $this->input->post('siteFavicon'), $path_images . $this->input->post('siteFavicon'));
            }
            $this->Hoosk_model->updateSettings();
            redirect(site_url('/admin/settings'));
        }
    }

    public function uploadLogo() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png';

        $this->load->library('upload', $config);
        foreach ($_FILES as $key => $value) {
            if (!$this->upload->do_upload($key)) {
                $error = array('error' => $this->upload->display_errors());
                echo 0;
            } else {
                echo '"' . $this->upload->data('file_name') . '"';
            }
        }
    }

    public function social() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));

        $this->data['social'] = $this->Hoosk_model->getSocial();
        $this->_views(array('admin/social'));
    }

    public function updateSocial() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));
        $this->Hoosk_model->updateSocial();
        redirect(BASE_URL . '/admin', 'refresh');
    }

    public function checkSession() {
        if (!$this->session->userdata('logged_in')) {
            echo 0;
        } else {
            echo 1;
        }
    }
}
