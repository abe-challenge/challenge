<?php

namespace ABE\Repositories;

use ABE\Database;
use PDO;

class BaseRepository
{
    /**
     * @var PDO
     */
    protected $databaseConnection;

    public function __construct()
    {
        $this->databaseConnection = Database::getConnection();
    }
}
