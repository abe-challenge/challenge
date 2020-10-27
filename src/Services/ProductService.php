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

class ProductService
{
    private $productRepository;
    private $productArticleMappingRepository;
    private $stockRepository;
    private $articleRepository;
    private $messageBus;

    public function __construct(
        ProductRepository $productRepository,
        ProductArticleMappingRepository $productArticleMappingRepository,
        StockRepository $stockRepository,
        ArticleRepository $articleRepository,
        MessageBus $messageBus
    ) {
        $this->productRepository = $productRepository;
        $this->productArticleMappingRepository = $productArticleMappingRepository;
        $this->stockRepository = $stockRepository;
        $this->articleRepository = $articleRepository;
        $this->messageBus = $messageBus;
    }

    public function getAllProductsAsEncoded(): ?string
    {
        return json_encode($this->getAllProducts()) ?? null;
    }

    public function addProductsFromUploadedFiles(array $uploadedFiles): void
    {
        if (empty($uploadedFiles) || empty($uploadedFiles['product_file'])) {
            throw new EmptyFileException();
        }

        $uploadedFile = $uploadedFiles['product_file'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new MalformedUploadException();
        }

        $decodedProducts = json_decode($uploadedFile->getStream()->read($uploadedFile->getStream()->getSize()));

        foreach ($decodedProducts->products as $decodedProduct) {
            $productId = isset($decodedProduct->id) ? $decodedProduct->id : uniqid();
            $this->productRepository->insert(
                $productId,
                $decodedProduct->name,
                isset($decodedProduct->price) ? (int) $decodedProduct->price : rand(1, 20)
            );

            foreach ($decodedProduct->contain_articles as $mapping) {
                $this->productArticleMappingRepository->insert($productId, (int) $mapping->art_id, (int) $mapping->amount_of);
            }
        }
    }

    public function getProductAsEncoded(string $productId): string
    {
        $productDto = $this->getProduct($productId);
        if ($productDto === null) {
            throw new ProductNotFoundException();
        }

        return json_encode($productDto);
    }

    public function updateProduct(string $productId, array $data): string
    {
        $productDto = $this->getProduct($productId);
        if ($productDto === null) {
            throw new ProductNotFoundException();
        }

        if (!empty($data['name']) || !empty($data['price'])) {
            $this->productRepository->update($productId, $data['name'] ?? $productDto->name, $data['price'] ?? $productDto->price);
        }

        return $this->getProductAsEncoded($productId);
    }

    public function deleteProduct(string $productId): void
    {
        if (!$this->hasProductId($productId)) {
            throw new ProductNotFoundException();
        }

        $this->productRepository->delete($productId);
    }

    public function sellProduct(string $productId): void
    {
        if (!$this->hasProductId($productId)) {
            throw new ProductNotFoundException();
        }

        $productDto = $this->getProduct($productId);
        if ($productDto === null || $productDto->stock < 1) {
            throw new NoStockException();
        }

        $this->stockRepository->decrease($productId);
    }

    private function getProduct(string $productId): ?ProductDto
    {
        return ProductDtoAssembler::assembleFromStatement(
            $this->productRepository->get($productId)
        );
    }

    /**
     * @return ProductDto[]
     */
    private function getAllProducts(): array
    {
        return ProductDtoAssembler::assembleMultipleFromStatement(
            $this->productRepository->getAll()
        );
    }

    private function hasProductId(string $productId): bool
    {
        return !empty($this->productRepository->get($productId)->fetch());
    }
}
