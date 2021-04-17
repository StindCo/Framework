<?php

namespace Framework\Utils;

use Framework\Utils\Request;

class ControllerBuilder
{
    /**
     * @var String $category
     */
    private string $category = "Controllers";
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $params = NULL;
    public function __construct($name = null)
    {
        if (!\is_null($name)) {
            $this->name = $name;
        }
    }
    /**
     * 
     *
     * @param String $name
     * @return ControllerBuilder
     */
    public function set_name(String $name):ControllerBuilder
    {
        $this->name = $name;
        return $this;
    }
    
    protected function creator(String $classe, $request, $router)
    {
        $classToLoad = \ucfirst($classe);
        $namespaceClass = "Controllers\\" . $classToLoad;
        if (\class_exists($namespaceClass)) {
            return new $namespaceClass($request, $router);
        } else {
            return false;
        }
    }
    public function get_instance(Request $request, Router $router)
    {
        return $this->creator($this->name, $request, $router);
    }
}
