<?php

namespace ABE\Services;

use ABE\DtoAssemblers\ProductDtoAssembler;
use ABE\Dtos\ProductDto;
use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Exceptions\NoStockException;
use ABE\Exceptions\ProductNotFoundException;
use ABE\Repositories\ProductArticleMappingRepository;
use ABE\Repositories\ProductRepository;
use Psr\Http\Message\UploadedFileInterface;

class ProductService
{
    private $stockService;
    private $productRepository;
    private $productArticleMappingRepository;

    public function __construct(
        StockService $stockService,
        ProductRepository $productRepository,
        ProductArticleMappingRepository $productArticleMappingRepository
    ) {
        $this->stockService = $stockService;
        $this->productRepository = $productRepository;
        $this->productArticleMappingRepository = $productArticleMappingRepository;
    }

    public function getAllProductsAsEncoded(): ?string
    {
        $encodedProducts = json_encode($this->getAllProducts());

        return is_string($encodedProducts) ? $encodedProducts : null;
    }

    /**
     * @param UploadedFileInterface[] $uploadedFiles
     */
    public function addProductsFromUploadedFiles(array $uploadedFiles): void
    {
        if (
            count($uploadedFiles) < 1 ||
            !isset($uploadedFiles['product_file'])
        ) {
            throw new EmptyFileException();
        }

        $uploadedFile = $uploadedFiles['product_file'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new MalformedUploadException();
        }

        $fileSize = $uploadedFile->getStream()->getSize();
        if ($fileSize === null) {
            throw new MalformedUploadException();
        }

        $decodedProducts = json_decode($uploadedFile->getStream()->read($fileSize));

        foreach ($decodedProducts->products as $decodedProduct) {
            $productId = isset($decodedProduct->id) ? $decodedProduct->id : uniqid();
            $this->productRepository->insert(
                $productId,
                $decodedProduct->name,
                isset($decodedProduct->price) ? (int) $decodedProduct->price : rand(1, 20)
            );
            $this->stockService->initializeProductStock($productId);

            foreach ($decodedProduct->contain_articles as $mapping) {
                $this->productArticleMappingRepository->insert($productId, (int) $mapping->art_id, (int) $mapping->amount_of);
            }

            $this->stockService->calculateProductStock($productId);
        }
    }

    public function getProductAsEncoded(string $productId): ?string
    {
        $productDto = $this->getProduct($productId);
        if ($productDto === null) {
            throw new ProductNotFoundException();
        }

        $encodedProduct = json_encode($productDto);

        return is_string($encodedProduct) ? $encodedProduct : null;
    }

    /**
     * @param string[] $data
     */
    public function updateProduct(string $productId, array $data): ?string
    {
        $productDto = $this->getProduct($productId);
        if ($productDto === null) {
            throw new ProductNotFoundException();
        }

        if (isset($data['name']) || isset($data['price'])) {
            $this->productRepository->update(
                $productId,
                $data['name'] ?? $productDto->name,
                isset($data['price']) ? (int) $data['price'] : $productDto->price
            );
            $this->stockService->calculateProductStock($productId);
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

        $this->stockService->decreaseProductStock($productId);
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
        return count($this->productRepository->get($productId)->fetch()) > 0;
    }
}
