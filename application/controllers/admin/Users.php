<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Admin_Controller {
    /**
     * __construct
     * Class construct
     * We will inherit the parent construct and
     * then check for a "logged" in session
     *
     * @access public
     */
    public function __construct() {
        //inherit the parent construct
        parent::__construct();

        //verify our session
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));
    }

    /**
     * index
     * This will display all of the users enrolled in this sites
     * as administrators
     *
     * @access public
     */
    public function index() {
        //Get users from database
        $this->data['users'] = $this->user_model->getAllUsers();

        //Load the view
        $this->_views(array('admin/users'));
    }

    /**
     * addUser
     * Allows adding additional administrators to
     * the site.
     * We setup form validation rules then either display
     * an add form or create the user
     *
     * @access public
     */
    public function addUser() {
        //Set validation rules
        $this->form_validation->set_rules('username', 'username', 'trim|alpha_dash|required|is_unique[hoosk_user.userName]');
        $this->form_validation->set_rules('email', 'email address', 'trim|required|valid_email|is_unique[hoosk_user.email]');
        $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('con_password', 'confirm password', 'trim|required|matches[password]');

        if ($this->form_validation->run() == false) {
            //Load the view
            $this->_views(array('admin/user_new'));
        } else {
            //Validation passed - add the user
            $this->user_model->createNewUser($this->input->post('username'), $this->input->post('email'), $this->input->post('password'));
            //Return to user list
            redirect(site_url('/admin/users'));
        }
    }

    /**
     * editUser
     * Allows an administrator to edit an existing account
     * You may modify the email and password fields
     *
     * @access public
     * @todo : Password changes should not be required
     * @todo : Username should be available to be changed
     */
    public function editUser() {
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
            $this->_views(array('admin/user_edit'));
        } else {
            //Update the user
            $this->Hoosk_model->updateUser($this->uri->segment(4));
            //Return to user list
            redirect(site_url('/admin/users'));
        }
    }

    /**
     * delete
     * Allows an administrator to delete an existing account
     * We verify the userID is valid, then we make our form
     * validation rules. If the validation passes we delete
     * the user otherwise we load the user_delete view
     *
     * @access public
     * @todo : You should not be able to delete the user who is currently logged in
     */
    public function delete() {
        //Get user details from database
        $this->data['users'] = $this->user_model->getUser($this->uri->segment(4));

        //if the userID isn't valid we won't have an array so lets error out
        if (count($this->data['users']) == 0) {
            show_error('Invalid Users ID!');
        }

        $this->form_validation->set_rules('deleteid', 'User ID', 'required|numeric'); //we should allow deleting of the currentUser

        if ($this->form_validation->run() == false) {
            $this->data['form'] = $this->Hoosk_model->getUser($this->uri->segment(4));
            $this->load->view('admin/user_delete.php', $this->data);
        } else {
            $this->Hoosk_model->removeUser($this->input->post('deleteid'));
            redirect(site_url('/admin/users'));
        }
    }
}
