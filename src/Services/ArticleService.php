<?php

namespace ABE\Services;

use ABE\DtoAssemblers\ArticleDtoAssembler;
use ABE\Dtos\ArticleDto;
use ABE\Exceptions\ArticleNotFoundException;
use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Repositories\ArticleRepository;
use Psr\Http\Message\UploadedFileInterface;

class ArticleService
{
    private $stockService;
    private $articleRepository;

    public function __construct(
        StockService $stockService,
        ArticleRepository $articleRepository
    ) {
        $this->stockService = $stockService;
        $this->articleRepository = $articleRepository;
    }

    public function getAllArticlesAsEncoded(): ?string
    {
        $encodedArticles = json_encode($this->getAllArticles());

        return is_string($encodedArticles) ? $encodedArticles : null;
    }

    /**
     * @param UploadedFileInterface[] $uploadedFiles
     */
    public function addArticlesFromUploadedFiles(array $uploadedFiles): void
    {
        if (
            count($uploadedFiles) < 1 ||
            !isset($uploadedFiles['article_file'])
        ) {
            throw new EmptyFileException();
        }

        $uploadedFile = $uploadedFiles['article_file'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new MalformedUploadException();
        }

        $fileSize = $uploadedFile->getStream()->getSize();
        if ($fileSize === null) {
            throw new MalformedUploadException();
        }

        $decodedArticles = json_decode($uploadedFile->getStream()->read($fileSize));

        foreach ($decodedArticles->inventory as $decodedArticle) {
            $articleId = (int) $decodedArticle->art_id;
            $this->articleRepository->insert(
                $articleId,
                $decodedArticle->name,
                (int) $decodedArticle->stock
            );

            $this->stockService->calculateStockForArticleUpdate($articleId);
        }
    }

    public function getArticleAsEncoded(int $articleId): ?string
    {
        $articleDto = $this->getArticle($articleId);
        if ($articleDto === null) {
            throw new ArticleNotFoundException();
        }

        $encodedArticle = json_encode($articleDto);

        return is_string($encodedArticle) ? $encodedArticle : null;
    }

    /**
     * @param string[] $data
     */
    public function updateArticle(int $articleId, array $data): ?string
    {
        $articleDto = $this->getArticle($articleId);
        if ($articleDto === null) {
            throw new ArticleNotFoundException();
        }

        if (isset($data['name']) || isset($data['stock'])) {
            $this->articleRepository->update(
                $articleId,
                $data['name'] ?? $articleDto->name,
                isset($data['stock']) ? (int) $data['stock'] : $articleDto->stock
            );
            $this->stockService->calculateStockForArticleUpdate($articleId);
        }

        return $this->getArticleAsEncoded($articleId);
    }

    public function deleteArticle(int $articleId): void
    {
        if (!$this->hasArticleId($articleId)) {
            throw new ArticleNotFoundException();
        }

        $this->articleRepository->delete($articleId);
    }

    private function getArticle(int $articleId): ?ArticleDto
    {
        return ArticleDtoAssembler::assembleFromStatement(
            $this->articleRepository->get($articleId)
        );
    }

    /**
     * @return ArticleDto[]
     */
    private function getAllArticles(): array
    {
        return ArticleDtoAssembler::assembleMultipleFromStatement(
            $this->articleRepository->getAll()
        );
    }

    private function hasArticleId(int $articleId): bool
    {
        return count($this->articleRepository->get($articleId)->fetch()) > 0;
    }
}
