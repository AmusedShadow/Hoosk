<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Admin_Controller {
    public function index() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));
        //Get users from database
        $this->data['users'] = $this->user_model->getAllUsers();

        //Load the view
        $this->views(array('admin/users'));
    }

    public function addUser() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));

        //Set validation rules
        $this->form_validation->set_rules('username', 'username', 'trim|alpha_dash|required|is_unique[hoosk_user.userName]');
        $this->form_validation->set_rules('email', 'email address', 'trim|required|valid_email|is_unique[hoosk_user.email]');
        $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('con_password', 'confirm password', 'trim|required|matches[password]');

        if ($this->form_validation->run() == false) {
            //Load the view
            $this->views(array('admin/user_new'));
        } else {
            //Validation passed - add the user
            $this->user_model->createNewUser($this->input->post('username'), $this->input->post('email'), $this->input->post('password'));
            //Return to user list
            redirect(site_url('/admin/users'));
        }
    }

    public function editUser() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));
        //Get user details from database
        $this->data['users'] = $this->user_model->getUser($this->uri->segment(4));

        //if the userID isn't valid we won't have an array so lets error out
        if (count($this->data['users']) == 0) {
            show_error('Invalid Users ID!');
        }

        //Set validation rules
        $this->form_validation->set_rules('email', 'email address', 'trim|required|valid_email|is_unique[hoosk_user.email.userID.' . $this->uri->segment(4) . ']');
        $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('con_password', 'confirm password', 'trim|required|matches[password]');

        if ($this->form_validation->run() === false) {
            //Load the view
            $this->views(array('admin/user_edit'));
        } else {
            //Update the user
            $this->Hoosk_model->updateUser($this->uri->segment(4));
            //Return to user list
            redirect(site_url('/admin/users'));
        }
    }

    public function delete() {
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));

        $this->form_validation->set_rules('deleteid', 'User ID', 'required|numeric');

        if ($this->form_validation->run() == false) {
            $this->data['form'] = $this->Hoosk_model->getUser($this->uri->segment(4));
            $this->load->view('admin/user_delete.php', $this->data);
        } else {
            $this->Hoosk_model->removeUser($this->input->post('deleteid'));
            redirect(site_url('/admin/users'));
        }
    }

    protected function views($views = array(), $data = array(), $header = true, $footer = true) {
        $this->data['currentUser'] = $this->adminUser;

        if ($header == true) {
            $this->data['header'] = $this->load->view('admin/header', $this->data, true);
        }

        if ($footer == true) {
            $this->data['footer'] = $this->load->view('admin/footer', $this->data, true);
        }

        foreach ($views as $view) {
            $this->load->view($view, $this->data);
        }
    }
}
