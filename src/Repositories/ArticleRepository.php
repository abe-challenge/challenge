<?php

namespace ABE\Repositories;

use PDOStatement;

class ArticleRepository extends BaseRepository
{
    public function getAll(): ?PDOStatement
    {
        $statement = $this->databaseConnection->query('SELECT `id`, `name`, `stock` FROM `articles`');

        return $statement instanceof PDOStatement ? $statement : null;
    }

    public function insert(int $articleId, string $name, int $stock): void
    {
        $this->databaseConnection->beginTransaction();
        $preparedStatement = $this->databaseConnection->prepare(
            'INSERT INTO `articles` VALUES (:articleId, :name, :stock) ON DUPLICATE KEY UPDATE `stock` = `stock` + :stock'
        );
        $preparedStatement->execute([
            'articleId' => $articleId,
            'name' => $name,
            'stock' => $stock,
        ]);
        $this->databaseConnection->commit();
    }

    public function get(int $articleId): PDOStatement
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'SELECT `id`, `name`, `stock` FROM `articles` WHERE `id` = :articleId'
        );
        $preparedStatement->execute(['articleId' => $articleId]);

        return $preparedStatement;
    }

    public function update(int $articleId, string $name, int $stock): void
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'UPDATE `articles` SET `name` = :name, `stock` = :stock WHERE `id` = :articleId'
        );
        $preparedStatement->execute([
            'articleId' => $articleId,
            'name' => $name,
            'stock' => $stock,
        ]);
    }

    public function delete(int $articleId): void
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'DELETE FROM `articles` WHERE `id` = :articleId LIMIT 1'
        );
        $preparedStatement->execute(['articleId' => $articleId]);
    }

    public function decreaseStock(int $articleId, int $decrement = 1): void
    {
        $this->databaseConnection->beginTransaction();
        $sql = "UPDATE `articles` SET `stock` = `stock` - $decrement WHERE id = :articleId";
        $preparedStatement = $this->databaseConnection->prepare($sql);
        $preparedStatement->execute(['articleId' => $articleId]);
        $this->databaseConnection->commit();
    }

    /**
     * @param int[] $articleIds
     */
    public function getMultipleStockInformation(array $articleIds): PDOStatement
    {
        $prepareStatementForWhereIn = implode(',', array_fill(0, count($articleIds), '?'));
        $preparedStatement = $this->databaseConnection->prepare(
            "SELECT `id`, `stock` FROM `articles` WHERE `id` IN ($prepareStatementForWhereIn)"
        );
        $preparedStatement->execute($articleIds);

        return $preparedStatement;
    }
}
