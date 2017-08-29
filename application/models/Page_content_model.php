<?php

class Page_content_model extends Eloquent {
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
    protected $table = 'hoosk_page_content';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'contentID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function search($term) {
        $results = array();

        $m      = $this->newInstance();
        $search = $m->select('contentID')->where('pageTitle', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'page_content',
                'column' => 'pageTitle',
                'id'     => $row->contentID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('contentID')->where('navTitle', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'page_content',
                'column' => 'navTitle',
                'id'     => $row->contentID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('contentID')->where('pageContentHTML', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'page_content',
                'column' => 'pageContentHTML',
                'id'     => $row->contentID,
            );
        }

        $m      = $this->newInstance();
        $search = $m->select('contentID')->where('jumbotronHTML', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'page_content',
                'column' => 'jumbotronHTML',
                'id'     => $row->contentID,
            );
        }

        return collect($results);
    }
}