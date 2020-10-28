<?php

namespace ABE\Dtos;

class ArticleDto
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $stock;

    public function __construct(int $id, string $name, int $stock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->stock = $stock;
    }
}
