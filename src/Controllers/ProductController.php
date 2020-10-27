<?php

namespace ABE\Controllers;

use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Services\ProductService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class ProductController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getAllProducts(Response $response)
    {
        $encodedProducts = $this->productService->getAllProductsAsEncoded();
        if ($encodedProducts === null) {
            $response->getBody()->write('Unable to get all products');
            return $response->withStatus(500);
        }

        $response->getBody()->write($encodedProducts);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function addProducts(Request $request, Response $response)
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

        return $response->withStatus(201);
    }

    public function getProduct(Response $response, $id)
    {
        $response->getBody()->write($id);
        return $response;
    }

    public function updateProduct(Response $response, $id)
    {
        $response->getBody()->write($id);
        return $response;
    }

    public function sellProduct(Response $response, $id)
    {
        $response->getBody()->write($id);
        return $response;
    }

    public function deleteProduct(Response $response, $id)
    {
        $response->getBody()->write($id);
        return $response;
    }
}
