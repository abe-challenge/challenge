<?php

namespace ABE\Controllers;

use ABE\Exceptions\ArticleNotFoundException;
use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Services\ArticleService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ArticleController
{
    private $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function getAllArticles(Response $response)
    {
        $encodedArticles = $this->articleService->getAllArticlesAsEncoded();
        if ($encodedArticles === null) {
            $response->getBody()->write('Unable to get all articles');
            return $response->withStatus(500);
        }

        $response->getBody()->write($encodedArticles);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function addArticles(Request $request, Response $response)
    {
        try {
            $this->articleService->addArticlesFromUploadedFiles($request->getUploadedFiles());
        } catch (EmptyFileException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        } catch (MalformedUploadException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }

        return $response->withStatus(201);
    }

    public function getArticle(Response $response, $id)
    {
        try {
            $response->getBody()->write($this->articleService->getArticleAsEncoded((int) $id));
        } catch (ArticleNotFoundException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        }

        return $response;
    }

    public function updateArticle(Response $response, $id)
    {
        $response->getBody()->write($id);
        return $response;
    }

    public function deleteArticle(Response $response, $id)
    {
        $response->getBody()->write($id);
        return $response;
    }
}
