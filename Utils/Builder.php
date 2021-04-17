<?php

namespace Framework\Utils;

use ReflectionClass;

class Builder
{
	private $category = "Utils";
	private $params = NULL;
	public function __construct()
	{
		return $this;
	}
	public function set_name(String $name)
	{
		$this->name = $name;
		return $this;
	}
	public function set_category(String $category)
	{
		$this->category = $category;
		return $this;
	}
	public function set_params($params)
	{
		$this->params = $params;
		return $this;
	}
	protected function creator(String $classe, String $category = null)
	{
		
	}
	public function get_instance($request, $router)
	{
		return $this->creator($this->name, $this->category);
	}
}
