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
                'mysql:dbname=' . $_ENV['MYSQL_DATABASE'] . ';host=' . $_ENV['MYSQL_SERVER_NAME'] . ';port=' . $_ENV['MYSQL_PORT'],
                $_ENV['MYSQL_USER'],
                $_ENV['MYSQL_PASSWORD']
            );
        }

        return self::$connection;
    }
}
