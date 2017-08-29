<?php

class Post_model extends Eloquent {
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
}