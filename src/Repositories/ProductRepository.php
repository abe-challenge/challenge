<?php

namespace ABE\Repositories;

use PDOStatement;

class ProductRepository extends BaseRepository
{
    public function getAll(): PDOStatement
    {
        return $this->databaseConnection->query(
            'SELECT `id`, `name`, `price` FROM `products`'
        );
    }

    public function insert(string $productId, string $name, int $price): void
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'INSERT INTO `products` VALUES (:productId, :name, :price)'
        );
        $preparedStatement->execute([
            'productId' => $productId,
            'name' => $name,
            'price' => $price,
        ]);
    }
}
