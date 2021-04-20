<?php

namespace StindCo\stinder\Utils;

use StindCo\stinder\Interfaces\Controllers;
use StindCo\stinder\Plugins\SessionManager;
use StindCo\stinder\Utils\Request;
use StindCo\stinder\Utils\Router;
use ReflectionClass;

abstract class AbstractController implements Controllers
{
    protected $requireSession = null;
    protected $response;
    public function __construct(Request $request, Router $router)
    {
        $reflect = new ReflectionClass(get_called_class());
        $name = strtolower($reflect->getShortName());
        $layout = $name . 'Layout';
        $this->request = $request;
        $this->router = $router;
        $this->set_response($request, $name, $layout);
        $this->sessionManager = new SessionManager();
        if(!is_null($this->requireSession)) {
            if($this->sessionManager->is_connected() != true) return $this->router->go_to("main");
        }
    }

    public function get_plugin($name)
    {
        return $this->request->plugins["Plugin\\{$name}"];
    }

    public function set_response(Request $request, String $prefix, String $layout = null)
    {
        $this->response = new Response($request, $prefix, $layout);
        return $this;
    }

    public function default_method(EntityManager $entityManager)
    {
    }

    public function error(EntityManager $entityManager)
    {
    }
}
