<?php

class Navigation_model extends Eloquent {
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
    protected $table = 'hoosk_navigation';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'navigationID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function search($term) {
        $results = array();

        $m = $this->newInstance();

        $search = $m->select('navigationID')->where('navSlug', 'LIKE', '%' . $term . '%')->get();
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'navigation',
                'column' => 'navSlug',
                'id'     => $row->navigationID,
            );
        }

        $m = $this->newInstance();

        $search = $m->select('navigationID')->where('navTitle', 'LIKE', '%' . $term . '%');
        foreach ($search as $row) {
            $results[] = array(
                'type'   => 'navigation',
                'column' => 'navTitle',
                'id'     => $row->navigationID,
            );
        }

        return collect($results);
    }
}