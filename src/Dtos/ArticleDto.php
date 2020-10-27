<?php

namespace ABE\Dtos;

class ArticleDto
{
    public $id;
    public $name;
    public $stock;

    public function __construct(int $id, string $name, int $stock)
    {
        $this->id = $id;   
        $this->name = $name;   
        $this->stock = $stock;   
    }
}
