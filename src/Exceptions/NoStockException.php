<?php

namespace ABE\Exceptions;

use Exception;

class NoStockException extends Exception
{
    public function __construct()
    {
        parent::__construct('No stock left for product');
    }
}
