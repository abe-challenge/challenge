<?php

namespace ABE\Exceptions;

use Exception;

class EmptyFileException extends Exception
{
    public function __construct()
    {
        parent::__construct("Uploaded file couldn't be read or empty");
    }
}