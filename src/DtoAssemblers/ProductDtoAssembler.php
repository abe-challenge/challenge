<?php

namespace ABE\DtoAssemblers;

use ABE\Dtos\ProductDto;
use PDOStatement;

class ProductDtoAssembler
{
    /**
     * @return ProductDto[]
     */
    public static function assembleMultipleFromStatement(PDOStatement $statement): array
    {
        $productDtos = [];
        foreach ($statement as $product) {
            $productDtos[] = new ProductDto(
                $product['id'],
                $product['name'],
                $product['price'],
                $product['stock']
            );
        }
        return $productDtos;
    }

    public static function assembleFromStatement(PDOStatement $statement): ?ProductDto
    {
        $product = $statement->fetch();
        if ($product === false) {
            return null;
        }

        return new ProductDto(
            $product['id'],
            $product['name'],
            $product['price'],
            $product['stock']
        );
    }
}