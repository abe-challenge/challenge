<?php

namespace ABE;

use PDO;
use PDOException;

class Database
{
    /**
     * @var PDO
     */
    private static $connection;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (!isset(self::$connection)) {
            self::$connection = new PDO(
                'mysql:dbname=challenge;host=db;port=3306',
                'mysqluser',
                'mysqlpassword'
            );
        }

        return self::$connection;
    }
}