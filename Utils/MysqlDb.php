<?php

namespace StindCo\stinder\Utils;

use StindCo\stinder\Interfaces\Db;
use StindCo\stinder\Traits\SqlDbTrait;

class MysqlDb extends Db
{
    use SqlDbTrait;
    public function get_connection(String $dns = null, String $username = null, String $password = null)
    {
        $this->connection = new \PDO($dns, $username, $password);
        var_dump($this);
    }
}
