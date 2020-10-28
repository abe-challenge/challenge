<?php

namespace ABE\Repositories;

use PDOStatement;

class ProductArticleMappingRepository extends BaseRepository
{
    public function insert(string $productId, int $articleId, int $amountOf)
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'INSERT INTO `product_article_mapping` VALUES (:productId, :articleId, :amountOf)'
        );
        $preparedStatement->execute([
            'productId' => $productId,
            'articleId' => $articleId,
            'amountOf' => $amountOf,
        ]);
    }

    public function getRequiredArticlesForProduct(string $productId): PDOStatement
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'SELECT `article_id`, `amount_of` FROM `product_article_mapping` WHERE `product_id` = :productId'
        );
        $preparedStatement->execute(['productId' => $productId]);
        return $preparedStatement;
    }

    public function getRelatedProductsForArticle(int $articleId): PDOStatement
    {
        $preparedStatement = $this->databaseConnection->prepare(
            'SELECT `product_id` FROM `product_article_mapping` WHERE `article_id` = :articleId'
        );
        $preparedStatement->execute(['articleId' => $articleId]);
        return $preparedStatement;
    }
}