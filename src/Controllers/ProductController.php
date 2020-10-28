<?php

namespace ABE\Controllers;

use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Exceptions\NoStockException;
use ABE\Exceptions\ProductNotFoundException;
use ABE\Services\ProductService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getAllProducts(Response $response): Response
    {
        $encodedProducts = $this->productService->getAllProductsAsEncoded();
        if ($encodedProducts === null) {
            $response->getBody()->write('Unable to get all products');

            return $response->withStatus(500);
        }

        $response->getBody()->write($encodedProducts);

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function addProducts(Request $request, Response $response): Response
    {
        try {
            $this->productService->addProductsFromUploadedFiles($request->getUploadedFiles());
        } catch (EmptyFileException $e) {
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(400);
        } catch (MalformedUploadException $e) {
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(400);
        }

        return $response->withStatus(302)->withHeader('Location', '/');
    }

    public function getProduct(Response $response, string $productId): Response
    {
        try {
            $encodedProduct = $this->productService->getProductAsEncoded($productId);

            if ($encodedProduct !== null) {
                $response->getBody()->write($encodedProduct);
            }
        } catch (ProductNotFoundException $e) {
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(404);
        }

        return $response;
    }

    public function updateProduct(Request $request, Response $response, string $productId): Response
    {
        try {
            $updatedProduct = $this->productService->updateProduct(
                $productId,
                is_array($request->getParsedBody()) ? $request->getParsedBody() : []
            );

            if ($updatedProduct !== null) {
                $response->getBody()->write($updatedProduct);
            }
        } catch (ProductNotFoundException $e) {
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(404);
        }

        return $response;
    }

    public function deleteProduct(Response $response, string $productId): Response
    {
        try {
            $this->productService->deleteProduct($productId);
        } catch (ProductNotFoundException $e) {
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(404);
        }

        return $response->withStatus(204);
    }

    public function sellProduct(Response $response, string $productId): Response
    {
        try {
            $this->productService->sellProduct($productId);
        } catch (ProductNotFoundException $e) {
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(404);
        } catch (NoStockException $e) {
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(400);
        }

        return $response->withStatus(204);
    }
}
