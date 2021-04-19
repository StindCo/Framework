<?php

namespace StindCo\stinder\Traits;

use StindCo\stinder\Utils\Response;

trait controllerAPI
{
    public $viewVariables = array();
    public $models = array();

    public function __construct(Object $request)
    {
        $this->request = $request;
    }


    public function get_plugin($name)
    {
        return $this->request->plugins["Plugin\\{$name}"];
    }

    public function set_renderer(String $prefix, String $layout)
    {
        $this->renderer = new Response($prefix, $layout);
        return $this;
    }

    public function default()
    {
    }

    public function error()
    {
    }
}
