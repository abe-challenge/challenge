<?php

$requestPath = $_SERVER["REQUEST_URI"];
if (preg_match('/\.(?:js|html)$/', $requestPath)) {
    return false;    // serve the requested resource as-is.
} elseif (strpos($requestPath, "/api/") !== 0) {
    readfile(__DIR__ . "/index.html");
    exit;
}

echo '<pre>';
var_dump(1);
exit;
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

// $container->set('myService', function () {
//     $settings = [...];
//     return new MyService($settings);
// });
$app->addErrorMiddleware(true, false, false);
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();
