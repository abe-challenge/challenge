<?php

namespace ABE\Services;

use ABE\DtoAssemblers\ArticleDtoAssembler;
use ABE\Dtos\ArticleDto;
use ABE\Exceptions\ArticleNotFoundException;
use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Repositories\ArticleRepository;
use Symfony\Component\Messenger\MessageBus;

class ArticleService
{
    private $articleRepository;
    private $messageBus;

    public function __construct(
        ArticleRepository $articleRepository,
        MessageBus $messageBus
    ) {
        $this->articleRepository = $articleRepository;
        $this->messageBus = $messageBus;
    }

    public function getAllArticlesAsEncoded(): ?string
    {
        return json_encode($this->getAllArticles()) ?? null;
    }

    public function getArticleAsEncoded(int $articleId): string
    {
        $articleDto = $this->getArticle($articleId);
        if ($articleDto === null) {
            throw new ArticleNotFoundException();
        }

        return json_encode($this->getArticle($articleId)) ?? null;
    }

    public function addArticlesFromUploadedFiles(array $uploadedFiles): void
    {
        if (empty($uploadedFiles) || empty($uploadedFiles['article_file'])) {
            throw new EmptyFileException();
        }

        $uploadedFile = $uploadedFiles['article_file'];
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new MalformedUploadException();
        }

        $decodedArticles = json_decode($uploadedFile->getStream()->read($uploadedFile->getStream()->getSize()));

        foreach ($decodedArticles->inventory as $decodedArticle) {
            $this->articleRepository->insert(
                (int) $decodedArticle->art_id,
                $decodedArticle->name,
                (int) $decodedArticle->stock
            );
        }
    }

    public function updateArticle(int $articleId, array $data): string
    {
        $articleDto = $this->getArticle($articleId);
        if ($articleDto === null) {
            throw new ArticleNotFoundException();
        }

        if (!empty($data['name']) || !empty($data['stock'])) {
            $this->articleRepository->update($articleId, $data['name'] ?? $articleDto->name, $data['stock'] ?? $articleDto->stock);
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
        return !empty($this->articleRepository->get($articleId)->fetch());
    }
}
