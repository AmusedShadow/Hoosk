<?php

class Page_meta_model extends Eloquent {
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
    protected $table = 'hoosk_page_meta';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'metaID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}