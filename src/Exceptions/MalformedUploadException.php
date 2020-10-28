<?php

namespace ABE\Exceptions;

use Exception;

class MalformedUploadException extends Exception
{
    public function __construct()
    {
        parent::__construct('Uploaded file is malformed');
    }
}
