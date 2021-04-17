<?php

namespace Framework\Utils;


/**
 * Classe Db pour la gestion de la base de donnée
 */
class Db
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private static $Instance;
    private function __construct($type)
    {
    }
    public static function connect($type, $dns = null, $username = null, $password = null): Db
    {
        if (self::$Instance == null) {
            if ($type == 'pgsql') {
                self::$Instance = new \PDO($dns, $username, $password);
            } elseif ($type == 'sqlite') {
                $db = (new SqliteDb());
                $db->get_connection($dns);
                self::$Instance = $db;
            } elseif ($type == 'mysql') {
                $db = new MysqlDb;
                $db->get_connection($dns, $username, $password);
                self::$Instance = $db;
            }
        }
        return new self($type);
    }
    /**
     * Singleton
     *
     * @return Db
     */
    public static function get_instance()
    {
        return self::$Instance;
    }
}
