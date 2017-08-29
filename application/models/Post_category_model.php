<?php

class Post_category_model extends Eloquent {
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
    protected $table = 'hoosk_post_category';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'categoryID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function search($term) {
        $results = array();

        $m      = $this->newInstance();
        $search = $m->select('categoryID')->where('categoryTitle', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'post_category',
                'column' => 'categoryTitle',
                'id'     => $row->categoryID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('categoryID')->where('categorySlug', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'post_category',
                'column' => 'categorySlug',
                'id'     => $row->categoryID,
            );
        }

        return collect($results);
    }

    public function getCategory($id) {
        $m = $this->newInstance();

        $query = $m->where('categoryID','=',$id)->first();
        $return = array();
        if (count($query)>0) {
            $return = $query->toArray();

            $CI = &get_instance();
            $CI->load->EloquentModel('Post_model');

            $return['counter'] = $CI->post_model->where('categoryID','=',$id)->count();
        }

        return $return;
    }
}