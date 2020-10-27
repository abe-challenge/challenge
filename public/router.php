<?php

$requestPath = $_SERVER["REQUEST_URI"];
if (preg_match('/\.(?:js|html)$/', $requestPath)) {
    return false;
} elseif (strpos($requestPath, "/api/") !== 0) {
    // Unknown route, serve index
    readfile(__DIR__ . "/index.html");
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
require_once 'index.php';