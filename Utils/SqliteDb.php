<?php

namespace StindCo\stinder\Utils;

use StindCo\stinder\Interfaces\Db;
use StindCo\stinder\Traits\SqlDbTrait;

class SqliteDb implements Db
{
    use SqlDbTrait;
    /**
     * permet de se connecter à la base de donnée
     *
     * @param String $dns
     * @param String $username
     * @param String $password
     * @return SqliteDb
     */
    public function get_connection(
        String $dns = null,
        String $username = null,
        String $password = null
    ): SqliteDb {
        $this->connection = new \PDO('sqlite:'.$dns);
        return $this;
    }
}
