<?php

if (strpos($_SERVER['REQUEST_URI'], '/api/') !== 0) {
    readfile(__DIR__.'/index.html');
    exit;
}

require __DIR__.'/../vendor/autoload.php';
require_once 'index.php';
