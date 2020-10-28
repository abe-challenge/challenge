<?php

namespace ABE\Repositories;

use PDOStatement;

class ProductRepository extends BaseRepository
{
    public function getAll(): ?PDOStatement
    {
        return $this->databaseConnection->query(
            'SELECT `id`, `name`, `price`, `stock`.`stock` FROM `products` inner join `stock` on `stock`.`product_id` = `products`.`id`'
        ) ?? null;
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

    public function get(string $productId): PDOStatement
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'SELECT `id`, `name`, `price`, `stock`.`stock` FROM `products` inner join `stock` on `stock`.`product_id` = `products`.`id` WHERE `products`.`id` = :productId'
        );
        $preparedStatement->execute(['productId' => $productId]);

        return $preparedStatement;
    }

    public function update(string $productId, string $name, int $price): void
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'UPDATE `products` SET `name` = :name, `price` = :price WHERE `id` = :productId'
        );
        $preparedStatement->execute([
            'productId' => $productId,
            'name' => $name,
            'price' => $price,
        ]);
    }

    public function delete(string $productId): void
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'DELETE FROM `products` WHERE `id` = :productId LIMIT 1'
        );
        $preparedStatement->execute(['productId' => $productId]);
    }
}
