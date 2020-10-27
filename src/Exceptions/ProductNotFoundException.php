<?php

namespace ABE\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct("Product not found");
    }
}