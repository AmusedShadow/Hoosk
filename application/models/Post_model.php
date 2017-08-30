<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends Eloquent {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hoosk_post';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'postID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function search($term) {
        $results = array();

        $m      = $this->newInstance();
        $search = $m->select('postID')->where('postURL', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'posts',
                'column' => 'postURL',
                'id'     => $row->postID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('postID')->where('postTitle', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'posts',
                'column' => 'postTitle',
                'id'     => $row->postID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('postID')->where('postExcerpt', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'posts',
                'column' => 'postExcerpt',
                'id'     => $row->postID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('postID')->where('postContentHTML', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'posts',
                'column' => 'postContentHTML',
                'id'     => $row->postID,
            );
        }

        return collect($results);
    }

    public function getPage($id) {
        $CI = &get_instance();
        $CI->load->EloquentModel('Post_category_model');

        $category = $CI->post_category_model->getTable();
        $me       = $this->getTable();

        $m     = $this->newInstance();
        $query = $m->leftJoin($category, $category . '.categoryID', '=', $me . '.categoryID')
            ->where($me . '.postID', '=', $id)
            ->first();

        $return = array();
        if (count($query) > 0) {
            $return = $query->toArray();
        }

        return $return;
    }
}