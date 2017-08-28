<?php

class Social_model extends Eloquent {
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hoosk_social';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'socialID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}