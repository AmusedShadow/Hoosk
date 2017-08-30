<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends Eloquent {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hoosk_user';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'userID';

    protected $fillable = array(
        'userName',
        'email',
        'password',
        'RS',
    );

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function getAllUsers() {
        $m     = $this->newInstance();
        $query = $m->get();

        $return = array();
        if (count($query) > 0) {
            foreach ($query as $row) {
                $return[] = $row->toArray();
            }
        }

        return $return;
    }

    public function createNewUser($username, $email, $password) {
        $m = $this->newInstance();

        $m->userName = $username;
        $m->email    = $email;
        $m->password = md5($password . SALT);
        $m->RS       = '';
        $m->save();

        return $m->userID;
    }

    public function getUser($id) {
        $m = $this->newInstance();

        $query = $m->find($id);

        $return = array();
        if (count($query) > 0) {
            $return[] = $query->toArray();
        }

        return $return;
    }
}