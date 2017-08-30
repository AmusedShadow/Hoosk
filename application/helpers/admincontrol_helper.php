<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admincontrol_helper {
    public static function is_logged_in($username) {
        $CI = &get_instance();
        $CI->load->library('session');
        $CI->load->helper('url');

        $userID    = $CI->session->userdata('userID');
        $username  = $CI->session->userdata('userName');
        $logged_in = $CI->session->userdata('logged_in');
        $crc       = $CI->session->userdata('crc');

        $checkCRC = hash('sha256', $CI->config->item('encryption_key') . '+' . SALT . '+' . $userID);
        $destroy  = false;

        if ((empty($userID)) || (is_null($userID)) || ($userID <= 0)) {
            $destroy = true;
        }

        if ((empty($username)) || is_null($username)) {
            //we should also verify this username is real
            $destroy = true;
        }

        if ((is_null($logged_in)) || ($logged_in != true)) {
            $destroy = true;
        }

        if ((is_null($crc)) || ($crc != $checkCRC)) {
            $destroy = true;
        }

        if ($destroy == true) {
            $CI->session->set_userdata(array(
                'userID'    => '',
                'userName'  => '',
                'logged_in' => '',
                'crc'       => '',
            ));

            $CI->session->sess_destroy();

            redirect(site_url('/admin/login'));
            exit;
        }
    }
}
