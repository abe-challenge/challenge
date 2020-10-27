<?php

namespace ABE\Repositories;

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
}