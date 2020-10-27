<?php

namespace ABE\Repositories;

class StockRepository extends BaseRepository
{
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
}