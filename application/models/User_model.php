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

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}