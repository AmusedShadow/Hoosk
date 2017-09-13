<?php

use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Events\Dispatcher;

class Events extends Dispatcher {
    /**
     * Create a new event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     * @return void
     */
    public function __construct(ContainerContract $container = null) {
        if (!in_array('container', array_keys(is_loaded()))) {
            $CI = &get_instance();
            $CI->load->library('container');

            $container = $CI->container;
        }

        parent::__construct($container);
    }
}