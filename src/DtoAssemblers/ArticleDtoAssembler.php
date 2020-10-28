<?php

namespace ABE\DtoAssemblers;

use ABE\Dtos\ArticleDto;
use PDOStatement;

class ArticleDtoAssembler
{
    /**
     * @return ArticleDto[]
     */
    public static function assembleMultipleFromStatement(PDOStatement $statement): array
    {
        $articleDtos = [];
        foreach ($statement as $article) {
            $articleDtos[] = new ArticleDto(
                $article['id'],
                $article['name'],
                $article['stock'],
            );
        }

        return $articleDtos;
    }

    public static function assembleFromStatement(PDOStatement $statement): ?ArticleDto
    {
        $article = $statement->fetch();
        if ($article === false) {
            return null;
        }

        return new ArticleDto(
            $article['id'],
            $article['name'],
            $article['stock'],
        );
    }
}
