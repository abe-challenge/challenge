<?php

namespace ABE\Repositories;

class StockRepository extends BaseRepository
{
    public function initializeProductStock(string $productId, int $stock = 0): void
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'INSERT INTO `stock` VALUES (:productId, :stock)'
        );
        $preparedStatement->execute([
            'productId' => $productId,
            'stock' => $stock
        ]);
    }

    public function decrease(string $productId, int $decrement = 1)
    {
        $this->databaseConnection->beginTransaction();
        $sql = "UPDATE `stock` SET `stock` = `stock` - $decrement WHERE product_id = :productId";
        $preparedStatement = $this->databaseConnection->prepare($sql);
        $preparedStatement->execute(['productId' => $productId]);
        $this->databaseConnection->commit();
    }

    public function increase(string $productId, int $increment = 1)
    {
        $this->databaseConnection->beginTransaction();
        $sql = "UPDATE `stock` SET `stock` = `stock` + $increment WHERE product_id = :productId";
        $preparedStatement = $this->databaseConnection->prepare($sql);
        $preparedStatement->execute(['productId' => $productId]);
        $this->databaseConnection->commit();
    }

    public function set(string $productId, int $stock = 0)
    {
        $this->databaseConnection->beginTransaction();
        $sql = "UPDATE `stock` SET `stock` = :stock WHERE product_id = :productId";
        $preparedStatement = $this->databaseConnection->prepare($sql);
        $preparedStatement->execute([
            'productId' => $productId,
            'stock' => $stock,
        ]);
        $this->databaseConnection->commit();
    }
}