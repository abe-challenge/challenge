<?php

namespace ABE\Repositories;

use PDOStatement;

class ArticleRepository extends BaseRepository
{
    public function getAll(): PDOStatement
    {
        return $this->databaseConnection->query(
            'SELECT `id`, `name`, `stock` FROM `articles`'
        );
    }

    public function insert(int $articleId, string $name, int $stock): void
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'INSERT INTO `articles` VALUES (:articleId, :name, :stock)'
        );
        $preparedStatement->execute([
            'articleId' => $articleId,
            'name' => $name,
            'stock' => $stock,
        ]);
    }

    public function get(int $articleId): PDOStatement
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'SELECT `id`, `name`, `stock` FROM `articles` WHERE `id` = :articleId'
        );
        $preparedStatement->execute(['articleId' => $articleId]);
        return $preparedStatement;
    }

    public function update(int $articleId, string $name, int $stock)
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
}
