<?php

class Banner_model extends Eloquent {
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
    protected $table = 'hoosk_banner';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'slideID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}