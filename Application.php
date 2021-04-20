<?php

namespace StindCo\stinder;

use StindCo\stinder\Utils\Db as UtilsDb;
use StindCo\stinder\Utils\EntityManager;
use StindCo\stinder\Utils\Request;
use StindCo\stinder\Utils\Router;
use ReflectionClass;

class Application
{
    /**
     * [$container description]
     * 
     * @var DIC
     */
    public  $container;
    /**
     * tableau des arrays
     * @var array
     */
    private array $pluggins = [];
    public EntityManager $entityManager;
    private String $env = "browser";
    public Request $request;
    public array  $dbConfigs = [];
    public Router $router;
    public array $configs = [];

    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router($this->request);
    }
    /**
     * Ajouter un plugin
     * @param object $plugins [description]
     */
    public function add_plugin(object $plugins): self
    {
        $reflexions = new ReflectionClass($plugins);
        $name = $reflexions->getName();
        $this->pluggins[$name] = $plugins;
        return $this;
    }

    /**
     *  ==============================================
     *      Le routage , Le CRUD Routing
     *  =============================================
     */
    public function otherwise($callback): void
    {
        $this->router->otherwise = $callback;
    }
    public function get($path, $infos): self
    {
        $this->router->routes['get'][$path] = $infos;
        return $this;
    }
    public function post($path, $infos): self
    {
        $this->router->routes['post'][$path] = $infos;
        return $this;
    }

    public function delete($path, $infos): self
    {
        $this->router->routes['delete'][$path] = $infos;
        return $this;
    }

    public function update($path, $infos): self
    {
        $this->router->routes['update'][$path] = $infos;
        return $this;
    }

    public function put($path, $infos): self
    {
        $this->router->routes['put'][$path] = $infos;
        return $this;
    }
    /**
     * ===================================================
     *      Les configurations de l'application
     * ===================================================
     */


    /**
     * Lance la configuration, appliques les configurations
     */
    public function run_configuration(): self
    {
        foreach ($this->configs as $key => $value) {
            $this->$key($value);
        }
        extract($this->dbConfigs);

        $this->database_management($type, $dns, $username, $password);


        return $this;
    }
    /**
     * Set the environnement
     *
     * @param String $env
     * @return void
     */
    public function env(String $env)
    {
        $this->env = $env;
        $this->request->env = $env;
        return $this;
    }
    /**
     * Return a env test
     */
    public function get_env()
    {
        return $this->env;
    }
    public function route_mode($type)
    {
        $this->request->set_route_mode($type);
        return $this;
    }

    public function entity_folder($folder)
    {
        $this->load_entityManager($folder);
    }
    /**
     * chargement de la gestion de la basse de donnée
     *
     * @param [type] $type
     * @param [type] $dns
     * @param [type] $username
     * @param [type] $password
     * @return self
     */
    public function database_management($type, $dns = null, $username = null, $password = null): self
    {
        UtilsDb::connect($type, $dns, $username, $password);

        return $this;
    }
    /**
     * Chargement des l'entity manager
     *
     * @return self
     */
    public function load_entityManager($folder): self
    {
        $this->entityManager = new EntityManager($folder);
        return $this;
    }
    /**
     *  =======================================
     *      Lancement de l'application
     *  =======================================
     */



    /**
     * lance l'application
     * @return self [description]
     */
    public function run()
    {
        $this->run_configuration();

        $this->request->plugins = $this->pluggins;

        $app = $this;

        $this->router->start($app);
    }
}
