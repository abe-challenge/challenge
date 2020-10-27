<?php

namespace ABE\Controllers;

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
        $encodedProducts = $this->productService->getAllProductsForResponse();
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
            $this->productService->addProductsFromFile($request->getUploadedFiles());
        } catch (\Throwable $th) {
            //throw $th;
        }
        $uploadedFiles = $request->getUploadedFiles();
        if (empty($uploadedFiles)) {
            $response->getBody()->write('No files uploaded');
            return $response->withStatus(400);
        }

        /**
         * @var UploadedFileInterface $uploadedFile
         */
        $uploadedFile = $request->getUploadedFiles()['product_file'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            // return error response
        }


        $response->getBody()->write($uploadedFile->getStream()->read($uploadedFile->getStream()->getSize()));

        return $response->withStatus(202);
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
