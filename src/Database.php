<?php

namespace ABE;

use PDO;

class Database
{
    /**
     * @var PDO|null
     */
    private static $connection;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (!self::$connection instanceof PDO) {
            self::$connection = new PDO(
                'mysql:dbname=challenge;host=db;port=3306',
                'mysqluser',
                'mysqlpassword'
            );
        }

        return self::$connection;
    }
}
