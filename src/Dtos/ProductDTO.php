<?php

namespace ABE\Dtos;

class ProductDto
{
    public $id;
    public $name;
    public $price;
    public $stock;

    public function __construct(string $id, string $name, int $price, int $stock)
    {
        $this->id = $id;   
        $this->name = $name;   
        $this->price = $price;   
        $this->stock = $stock;   
    }
}