<?php

namespace ABE\Dtos;

class ProductDto
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $price;

    /**
     * @var int
     */
    public $stock;

    public function __construct(string $id, string $name, int $price, int $stock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }
}
