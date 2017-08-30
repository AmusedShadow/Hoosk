<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends Admin_Controller {
    /**
     * construct
     * Class construct
     *
     * @access public
     */
    public function __construct() {
        parent::__construct();

        //check session exists
        Admincontrol_helper::is_logged_in($this->session->userdata('userName'));
    }

    /**
     * index
     * The index page. This displays a lists of posts
     *
     * @access public
     */
    public function index() {
        //Get posts from database
        $this->data['posts'] = $this->post_model->getPosts();

        //Load the view
        $this->_views(array('admin/posts'));
    }

    /**
     * addPost
     * Allows an administrator to add a post. We setup
     * some form validation rules and if they don't run or fail
     * we will display an entry form.
     * Otherwise we save the post and redirect the user back to the
     * listing page
     *
     * @access public
     */
    public function addPost() {
        //Set validation rules
        $this->form_validation->set_rules('postURL', 'post URL', 'trim|alpha_dash|required|is_unique[hoosk_post.postURL]');
        $this->form_validation->set_rules('postTitle', 'post title', 'trim|required');
        $this->form_validation->set_rules('postExcerpt', 'post excerpt', 'trim|required');

        if ($this->form_validation->run() === false) {
            $this->data['categories'] = $this->Hoosk_model->getCategories();
            $this->_views(array('admin/post_new'));
        } else {
            //Validation passed
            if ($this->input->post('postImage') != "") {
                //path to save the image
                $path_upload = $_SERVER["DOCUMENT_ROOT"] . '/uploads/';
                $path_images = $_SERVER["DOCUMENT_ROOT"] . '/images/';
                //moving temporary file to images folder
                rename($path_upload . $this->input->post('postImage'), $path_images . $this->input->post('postImage'));
            }

            //Add the post
            $this->load->library('Sioen');
            $this->Hoosk_model->createPost();
            //Return to post list
            redirect(site_url('/admin/posts'));
        }
    }

    /**
     * editPost
     * The allows an administrator to edit an existing post
     * We setup some validation rules. If the validation
     * hasn't run or failed display the edit form other
     * wise save our modification and redirect
     *
     * @access public
     * @todo : SECURITY CONCERN - Uploads should be sanitized/verified
     * @todo : We should put in a validation rule to make sure this postID exists
     */
    public function editPost() {
        //Set validation rules
        $this->form_validation->set_rules('postURL', 'post URL', 'trim|alpha_dash|required|is_unique[hoosk_post.postURL.postID.' . $this->uri->segment(4) . ']');
        $this->form_validation->set_rules('postTitle', 'post title', 'trim|required');

        if ($this->form_validation->run() == false) {
            //Validation failed
            $this->data['categories'] = $this->Hoosk_model->getCategories();
            //Get post details from database
            $this->data['posts'] = $this->Hoosk_model->getPost($this->uri->segment(4));

            $this->_views(array('admin/post_edit'));
        } else {
            //Validation passed
            if ($this->input->post('postImage') != "") {
                //path to save the image
                $path_upload = $_SERVER["DOCUMENT_ROOT"] . '/uploads/';
                $path_images = $_SERVER["DOCUMENT_ROOT"] . '/images/';
                //moving temporary file to images folder
                rename($path_upload . $this->input->post('postImage'), $path_images . $this->input->post('postImage'));
            }
            //Update the post
            $this->load->library('Sioen');
            $this->Hoosk_model->updatePost($this->uri->segment(4));
            //Return to post list
            redirect(site_url('/admin/posts'));
        }
    }

    /**
     * delete
     * Allows an administrator to delete an existing post
     * We setup some validation rules. If the validation
     * hasn't run or fails show the delete form otherwise
     * delete the post.
     *
     * @access public
     * @todo : Form validation rule to make sure the postID exists
     */
    public function delete() {
        $this->form_validation->set_rules('deleteid', 'Post ID', 'required|numeric');

        if ($this->form_validation->run() == false) {
            $this->data['form'] = $this->Hoosk_model->getPost($this->uri->segment(4));
            $this->load->view('admin/post_delete.php', $this->data);
        } else {
            $this->Hoosk_model->removePost($this->input->post('deleteid'));
            redirect(site_url('/admin/posts'));
        }
    }
}
