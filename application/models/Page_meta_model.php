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

    public function search($term) {
        $results = array();

        $m      = $this->newInstance();
        $search = $m->select('metaID')->where('pageKeywords', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'page_meta',
                'column' => 'pageKeywords',
                'id'     => $row->metaID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('metaID')->where('pageDescription', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'page_meta',
                'column' => 'pageDescription',
                'id'     => $row->metaID,
            );
        }

        return collect($results);
    }

    public function getMeta($id) {
        $CI = &get_instance();
        $CI->load->EloquentModel('Page_content_model');
        $CI->load->EloquentModel('Page_attributes_model');

        $content = $CI->page_content_model->getTable();
        $attributes = $CI->page_attributes_model->getTable();
        $me = $this->getTable();

        $m = $this->newInstance();
        $query = $m->leftJoin($content,$content.'.pageID','=',$me.'.pageID')
        ->leftJoin($attributes,$attributes.'.pageID','=',$me.'.pageID')
        ->where($me.'.metaID','=',$id)
        ->first();

        $return = array();
        if (count($query)>0) {
            $return = $query->toArray();
        }

        return $return;
    }
}