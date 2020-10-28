<?php

namespace ABE\Services;

use ABE\Dtos\ProductDto;
use ABE\DtoAssemblers\ProductDtoAssembler;
use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Exceptions\NoStockException;
use ABE\Exceptions\ProductNotFoundException;
use ABE\Repositories\ArticleRepository;
use ABE\Repositories\ProductArticleMappingRepository;
use ABE\Repositories\ProductRepository;
use ABE\Repositories\StockRepository;
use Symfony\Component\Messenger\MessageBus;

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

    public function initializeProductStock(string $productId)
    {
        $this->stockRepository->initializeProductStock($productId);
    }

    public function decreaseProductStock(string $productId, int $decrement = 1)
    {
        $dependentArticles = $this->productArticleMappingRepository->getRequiredArticlesForProduct($productId)->fetchAll();
        foreach ($dependentArticles as $dependentArticle) {
            $this->decreaseArticleStock($dependentArticle['article_id'], $dependentArticle['amount_of']);
        }

        $this->stockRepository->decrease($productId, $decrement);
    }

    public function calculateStockForArticleUpdate(int $articleId)
    {
        $relatedProducts = $this->productArticleMappingRepository->getRelatedProductsForArticle($articleId)->fetchAll();
        foreach ($relatedProducts as $relatedProduct) {
            $this->calculateProductStock($relatedProduct['product_id']);
        }
    }

    public function calculateProductStock(string $productId)
    {
        $requiredArticles = $this->productArticleMappingRepository->getRequiredArticlesForProduct($productId)->fetchAll();
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
            if ($amountInformationIndexedById[$articleStockInformation['id']] === 0) {
                $amountOfAvailableProductsPerArticle[] = 0;

                continue;
            }

            $amountOfAvailableProductsPerArticle[] = (int) floor(
                $articleStockInformation['stock']/$amountInformationIndexedById[$articleStockInformation['id']]
            );
        }

        $this->stockRepository->set($productId, min($amountOfAvailableProductsPerArticle));
    }

    private function decreaseArticleStock(int $articleId, int $decrement = 1)
    {
        $this->articleRepository->decreaseStock($articleId, $decrement);
        $this->calculateStockForArticleUpdate($articleId);
    }
}