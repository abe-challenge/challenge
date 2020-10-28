<?php

namespace ABE\Services;

use ABE\Repositories\ArticleRepository;
use ABE\Repositories\ProductArticleMappingRepository;
use ABE\Repositories\StockRepository;

class StockService
{
    private $productArticleMappingRepository;
    private $stockRepository;
    private $articleRepository;

    public function __construct(
        StockRepository $stockRepository,
        ProductArticleMappingRepository $productArticleMappingRepository,
        ArticleRepository $articleRepository
    ) {
        $this->stockRepository = $stockRepository;
        $this->productArticleMappingRepository = $productArticleMappingRepository;
        $this->articleRepository = $articleRepository;
    }

    public function initializeProductStock(string $productId): void
    {
        $this->stockRepository->initializeProductStock($productId);
    }

    public function decreaseProductStock(string $productId, int $decrement = 1): void
    {
        $dependentArticles = $this->productArticleMappingRepository->getRequiredArticlesForProduct($productId)->fetchAll();
        if ($dependentArticles === false) {
            return;
        }

        foreach ($dependentArticles as $dependentArticle) {
            $this->decreaseArticleStock($dependentArticle['article_id'], $dependentArticle['amount_of']);
        }

        $this->stockRepository->decrease($productId, $decrement);
    }

    public function calculateStockForArticleUpdate(int $articleId): void
    {
        $relatedProducts = $this->productArticleMappingRepository->getRelatedProductsForArticle($articleId)->fetchAll();
        if ($relatedProducts === false) {
            return;
        }

        foreach ($relatedProducts as $relatedProduct) {
            $this->calculateProductStock($relatedProduct['product_id']);
        }
    }

    public function calculateProductStock(string $productId): void
    {
        $requiredArticles = $this->productArticleMappingRepository->getRequiredArticlesForProduct($productId)->fetchAll();
        if ($requiredArticles === false) {
            return;
        }

        $articleStockInformations = $this->articleRepository->getMultipleStockInformation(array_reduce(
            $requiredArticles,
            function ($accumulator, $dependentArticle) {
                $accumulator[] = $dependentArticle['article_id'];

                return $accumulator;
            },
            []
        ));

        $amountInformationIndexedById = array_reduce(
            $requiredArticles,
            function ($accumulator, $dependentArticle) {
                $accumulator[$dependentArticle['article_id']] = $dependentArticle['amount_of'];

                return $accumulator;
            },
            []
        );

        $amountOfAvailableProductsPerArticle = [];
        foreach ($articleStockInformations as $articleStockInformation) {
            $articleId = $articleStockInformation['id'];
            if ($amountInformationIndexedById[$articleId] === 0) {
                $amountOfAvailableProductsPerArticle[] = 0;

                continue;
            }

            $amountOfAvailableProductsPerArticle[] = (int) floor(
                $articleStockInformation['stock'] / $amountInformationIndexedById[$articleId]
            );
        }

        $minimumProductStock = min($amountOfAvailableProductsPerArticle);
        $this->stockRepository->set(
            $productId,
            is_int($minimumProductStock) ? $minimumProductStock : 0
        );
    }

    private function decreaseArticleStock(int $articleId, int $decrement = 1): void
    {
        $this->articleRepository->decreaseStock($articleId, $decrement);
        $this->calculateStockForArticleUpdate($articleId);
    }
}
