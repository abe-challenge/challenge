<?php

$requestPath = $_SERVER["REQUEST_URI"];
if (preg_match('/\.(?:js|html)$/', $requestPath)) {
    return false;    // serve the requested resource as-is.
} elseif (strpos($requestPath, "/api/") !== 0) {
    readfile(__DIR__ . "/index.html");
    exit;
}

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/api/products', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();
