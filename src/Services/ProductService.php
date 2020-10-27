<?php

namespace ABE\Services;

use ABE\DTO\ProductDTO;
use ABE\Repositories\ProductRepository;
use ABE\Validators\ProductValidator;
use Symfony\Component\Messenger\MessageBus;

class ProductService
{
    private $productRepository;
    private $productValidator;
    private $messageBus;

    public function __construct(
        ProductRepository $productRepository,
        ProductValidator $productValidator,
        MessageBus $messageBus
    ) {
        $this->productRepository = $productRepository;
        $this->productValidator = $productValidator;
        $this->messageBus = $messageBus;
    }

    public function getAllProductsForResponse(): ?string
    {
        return json_encode($this->getAllProducts()) ?? null;
    }

    public function addProductsFromFile(array $uploadedFiles): void
    {
        if (empty($uploadedFiles)) {
            // asd
        }

        if (empty($uploadedFiles['product_file'])) {
            // qwe
        }

        $uploadedFile = $uploadedFiles['product_file'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            //zxc
        }

        $fileContent = $uploadedFile->getStream()->read($uploadedFile->getStream()->getSize());

        try {
            // $this->productValidator->validateEncoded($fileContent);
        } catch (\Throwable $th) {
            // fgh
        }

        foreach (json_decode($fileContent) as $product) {
            # code...
        }


        echo '<pre>';
        var_dump(json_decode($fileContent));
        exit;
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
