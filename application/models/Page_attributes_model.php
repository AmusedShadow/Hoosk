<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_attributes_model extends Eloquent {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hoosk_page_attributes';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'pageID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function search($term) {
        $results = array();

        $m = $this->newInstance();

        $search = $m->select('pageID')->where('pageURL', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'page_attributes',
                'column' => 'pageURL',
                'id'     => $row->pageID,
            );
        }

        return collect($results);
    }
}