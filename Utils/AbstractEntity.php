<?php

namespace StindPattern\Utils;

use StindPattern\Utils\Data;
use StindPattern\Utils\Db;

abstract class AbstractEntity extends Data
{

    protected static $database;

    public function __construct()
    {

        self::$database = Db::$Instance;
    }

    abstract public function create();
    abstract public function update();
    abstract public function delete();
    abstract public function get_it(Data $data);
}
