<?php

use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Database\Capsule\Manager;

class Capsule extends Manager {
    /**
     * Create a new database capsule manager.
     *
     * @param  \Illuminate\Container\Container|null  $container
     * @return void
     */
    public function __construct(ContainerContract $container = null) {
        $CI = &get_instance();

        if (!in_array('container', array_keys(is_loaded()))) {
            $CI->load->library('container');
        }
        $container = $CI->container;

        if (!in_array('events', array_keys(is_loaded()))) {
            $CI->load->library('events');
        }
        $events = $CI->events;

        parent::__construct($container);

        $this->setEventDispatcher($events);
        $this->setAsGlobal();
        $this->bootEloquent();

        $CI->config->load('database', true);
        $config = $CI->config->item('database');
        foreach ($config as $name => $data) {
            $this->addConnection(array(
                'driver'    => $data['subdriver'],
                'host'      => $data['hostname'],
                'database'  => $data['database'],
                'username'  => $data['username'],
                'password'  => $data['password'],
                'charset'   => $data['char_set'],
                'collation' => $data['dbcollat'],
                'prefix'    => $data['dbprefix'],
            ));
        }
    }
}