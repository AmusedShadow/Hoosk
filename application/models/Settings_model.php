<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends Eloquent {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hoosk_settings';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'settingID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}