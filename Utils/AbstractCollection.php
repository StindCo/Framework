<?php

namespace StindPattern\Utils;

use StindPattern\Utils\Db;
use StindPattern\Utils\Data;
use StindPattern\Utils\EntityBuilder;

abstract class abstractCollection extends Data
{
    protected static $database;
    public function __construct()
    {
        self::$database = Db::$Instance;
    }
    protected function entitify(?array $data, String $name)
    {
        $builder = new EntityBuilder();
        $builder->set_name($name);
        $entity = $builder->get_instance();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = $entity->get_serialization($data[$i]);
        }

        return $data;
    }
    abstract public function create();
    abstract public function update_some();
    abstract public function delete_some();
    abstract public function get_all();
}
