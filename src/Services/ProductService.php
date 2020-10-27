<?php

namespace ABE\Services;

use ABE\DTO\ProductDTO;
use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Repositories\ProductArticleMappingRepository;
use ABE\Repositories\ProductRepository;
use Symfony\Component\Messenger\MessageBus;

class ProductService
{
    private $productRepository;
    private $productArticleMappingRepository;
    private $messageBus;

    public function __construct(
        ProductRepository $productRepository,
        ProductArticleMappingRepository $productArticleMappingRepository,
        MessageBus $messageBus
    ) {
        $this->productRepository = $productRepository;
        $this->productArticleMappingRepository = $productArticleMappingRepository;
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

    /**
     * @return ProductDTO[]
     */
    private function getAllProducts(): array
    {
        $products = [];
        foreach ($this->productRepository->getAll() as $product) {
            $productDto = new ProductDTO();
            $productDto->id = $product['id'] ?? null;
            $productDto->name = $product['name'] ?? null;
            $productDto->price = $product['price'] ?? null;
            $productDto->stock = $product['stock'] ?? null;

            $products[] = $productDto;
        }

        return $products;
    }
}
