<?php

namespace StindCo\stinder\Utils;

use StindCo\stinder\Utils\Data;

class Request
{
	public String $controller;
	public ?String $action;
	public ?String $params;
	public ?String $env = "browser";
	public String $route_mode;
	public Server $server;

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		$this->GET_DATA = new Data($_GET);
		$this->POST_DATA = new Data($_POST);
		$this->server = new Server(new Data($_SERVER));
		$this->PUT_DATA = $this->daa();
	}
	public function set_route_mode($type)
	{
		$this->route_mode = $type;
	}
	public function get_infos()
	{
		if ($this->route_mode == 'component-action') {
			$this->path = $this->parse_url();
			$this->method = $this->get_method();
		} else {
			$this->path = $this->server->get_pathinfo() ?? "/";
			$this->method = $this->get_method();
		}
	}
	/**
	 * Permet de parser l'url
	 * @param  String | Null $route [description]
	 * @return Self        [description]
	 */
	public function parse_url(?String $route = null): String
	{
		if (is_null($route)) {
			$this->controller = $controller = $this->GET_DATA->ref_component ?? "";
			$this->action = $action = $this->GET_DATA->ref_action ?? "";
			$this->params = $params = $this->GET_DATA->ref_params ?? "";

			$url = "/{$this->controller}/{$this->action}/{$this->params}";
			if ($params == null or $params == "") $url = "/{$this->controller}/{$this->action}";
			if ($action == null or $action == "") {
				$url = "/{$this->controller}/.../{$this->params}";
				if ($params == null) $url = "/{$this->controller}";
			}
			if ($controller == "") $url = "/";

			return $url;
		} else {
			if ($this->route_mode == "component-action") {
				$url = explode("/", $route);
				$controller = $url[1];
				$action = $url[2];
				$params = $url[3];
				$url = "?ref_component={$controller}&ref_action={$action}&ref_params={$params}";
				if ($params == null or $params == "") $url = "?ref_component={$controller}&ref_action={$action}";
				if ($action == null or $action == "") {
					$url = "?ref_component={$controller}&ref_action={$action}";
					if ($params == null) $url = "?ref_component={$controller}";
				}
				return $url;
			} else {
				return $route;
			}
		}
		return "";
	}
	public function get_url()
	{
		return  $this->server->get_pathinfo();
	}
	/**
	 * Retourne le controlleur
	 *
	 * @return String $controller
	 */
	public function get_controller()
	{
		return $this->controller;
	}
	public function get_env()
	{
		return $this->env;
	}
	/**
	 * Retourne l'action
	 *
	 * @return String $action
	 */
	public function get_action()
	{
		return $this->action;
	}
	public function get_method()
	{
		return strtolower($this->server->get_method());
	}
	public function get_path()
	{
		return $this->path;
	}

	public function daa()
	{
		$data = file_get_contents("php://input");
		$data = json_decode($data);
		return $data;
	}
}
