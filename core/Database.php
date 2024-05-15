<?php

namespace core;

use \src\Config;

class Database
{
    private static \PDO $pdo;

    public static function getInstance(): \PDO
    {
        if (!isset(self::$pdo)) {
            self::$pdo = new \PDO(
                Config::DB_DRIVER .
                ":dbname=" . Config::DB_DATABASE .
                ";host=" . Config::DB_HOST,
                Config::DB_USER,
                Config::DB_PASS
            );
        }
        return self::$pdo;
    }

    /**
     * Default implementation is ignored
     */
    private function __construct()
    {
    }

    /**
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * @return void
     */
    private function __wakeup()
    {
    }
}