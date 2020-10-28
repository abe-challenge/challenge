<?php

namespace ABE\Exceptions;

use Exception;

class ArticleNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Article not found');
    }
}
