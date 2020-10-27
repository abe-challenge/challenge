<?php

namespace ABE\Services;

use ABE\Dtos\ProductDto;
use ABE\DtoAssemblers\ProductDtoAssembler;
use ABE\Exceptions\EmptyFileException;
use ABE\Exceptions\MalformedUploadException;
use ABE\Exceptions\NoStockException;
use ABE\Exceptions\ProductNotFoundException;
use ABE\Repositories\ArticleRepository;
use ABE\Repositories\ProductArticleMappingRepository;
use ABE\Repositories\ProductRepository;
use ABE\Repositories\StockRepository;
use Symfony\Component\Messenger\MessageBus;

class StockService
{
    private $productRepository;
    private $productArticleMappingRepository;
    private $stockRepository;
    private $articleRepository;

    public function __construct()
    {
        
    }
}