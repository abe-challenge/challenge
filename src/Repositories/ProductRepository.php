<?php

namespace ABE\Repositories;

use PDOStatement;

class ProductRepository
{
    public function getAll(): PDOStatement
    {
        return $this->databaseConnection->query(
            'SELECT `id`, `name`, `price` FROM `products`'
        );
    }
}
