<?php

namespace StindCo\stinder\Utils;

use StindCo\stinder\Application;

/**
 * Classe Dispatcher
 * La plus importante
 */


class Router
{
    /**
     * @inject
     * [$request description]
     * @var Request [type]
     */
    private Request $request;
    /**
     * l'instance de l'entityManager
     * @var EntityManager $entityManager
     */
    private EntityManager $entityManager;
    private $current;
    private ?array $routeParams = [];
    public $routes = [];
    /**
     * La fonction demarreur du routing 
     *
     * @param Request $request
     * @param EntityManager $entityManager
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public  function start(Application $app)
    {
        $this->request->get_infos();

        $this->app = $app;
        $method = $this->request->get_method();
        $path = $this->request->get_path();


        return $this->resolve($method, $path);
    }
    public function go_to(String $route)
    {
        $url = $this->request->parse_url($route);
        $this->redirect($url);
    }
    /**
     * [redirect description]
     * @param  [type] $controller [description]
     * @param  [type] $action     [description]
     * @param  [type] $params     [description]
     * @return [type]             [description]
     */
    private function redirect(String $url)
    {
        \header('Location: ' . $url);
    }

    /**
     * Le resoudreur de routage, elle rend le routage dynamique
     *
     * @param [type] $method
     * @param [type] $path
     * @return void
     */
    private function path_resolver($method, $path)
    {
        $urlData = explode("/", $path);
        foreach ($this->routes[$method] as $key => $value) {
            $routesData = explode("/", $key);
            if (count($routesData) == count($urlData)) {
                $ok = true;
                for ($i = 0; $i < count($urlData) && $ok == true; $i++) {
                    $params = explode(":", $routesData[$i]);
                    if (count($params) == 1) {
                        if ($urlData[$i] != $routesData[$i]) {
                            $ok = false;
                        }
                        continue;
                    } else {
                        $parameters[$params[1]] = $urlData[$i];
                        continue;
                    }
                }
                if ($ok == true) {
                    $this->routeParams = $parameters;
                    $callback = $value;
                    break;
                }
            } else {
                continue;
            }

            if (count($routesData) == 1) {
                $callback = $this->routes[$method][$key];
                break;
            }
        }

        return $callback;
    }

    /**
     * 
     *
     * @param [type] $method
     * @param [type] $path
     * @return void
     */
    public function resolve($method, $path)
    {
        if (($callback = $this->path_resolver($method, $path)) != null) {
            if (is_array($callback)) {
                $functionName = $callback[1];
                try {
                    return (new $callback[0]($this->request, $this))
                        ->$functionName(
                            $this->app->entityManager,
                            new Form($this->request),
                            $this->app
                        );
                } catch (\Throwable $th) {
                    throw new \Exception("Le controlleur {$callback[0]} ne contient pas d'action '{$callback[1]}' ", 1);
                }
            } else {
                $callback($this->app);
            }
        } else {
            if (isset($this->otherwise)) {
                $callback = $this->otherwise;
                if (is_array($callback)) {
                    $functionName = $callback[1];
                    try {
                        return (new $callback[0]($this->request, $this))
                            ->$functionName(
                                $this->app->entityManager,
                                new Form($this->request),
                                $this->app
                            );
                    } catch (\Throwable $th) {
                        throw new \Exception("Le controlleur {$callback[0]} ne contient pas d'action '{$callback[1]}' ", 1);
                        
                    }
                } else {
                    $callback($this->app);
                }
            }
        }
    }

    public function get_routeParams()
    {
        return $this->routeParams;
    }
}
