<?php

namespace Framework\Utils;

use ReflectionClass;

abstract class AbstractModel extends Data
{
    protected $no_attributes = [];

    public function __construct()
    {
    }
    public function __set($name, $value)
    {
        $this->no_attributes[$name] = $value;
    }
    public function __get($name)
    {
        return $this->no_attributes[$name];
    }
    public function setData(Data $data)
    {
        $reflect = new ReflectionClass($this);
        $attributes = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        for ($i = 0; $i < count($attributes); $i++) {
            $name = $attributes[$i]->getName();
            if($name == "id" OR $data->$name == null) continue;
            
            $this->$name = $data->$name;
        }
        return $this;
    }
}
