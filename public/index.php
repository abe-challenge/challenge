<?php

use ABE\Controllers\ArticleController;
use ABE\Controllers\ProductController;
use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;

$containerBuilder = new ContainerBuilder();
// TODO: Uncomment below
// $containerBuilder->enableCompilation(sys_get_temp_dir());
// $containerBuilder->writeProxiesToFile(true, sys_get_temp_dir());
// $containerBuilder->useAnnotations(false);
$app = Bridge::create($containerBuilder->build());

$app->group('/api', function (RouteCollectorProxy $routeCollectorProxy) {
    $routeCollectorProxy->group('/articles', function (RouteCollectorProxy $articlesCollector) {
        $articlesCollector->get('', [ArticleController::class, 'getAllArticles']);
        $articlesCollector->post('', [ArticleController::class, 'addArticles']);

        $articlesCollector->group('/{articleId:[0-9]+}', function (RouteCollectorProxy $articleCollector) {
            $articleCollector->get('', [ArticleController::class, 'getArticle']);
            $articleCollector->post('', [ArticleController::class, 'updateArticle']);
            $articleCollector->delete('', [ArticleController::class, 'deleteArticle']);
        });
    });

    $routeCollectorProxy->group('/products', function (RouteCollectorProxy $productsCollector) {
        $productsCollector->get('', [ProductController::class, 'getAllProducts']);
        $productsCollector->post('', [ProductController::class, 'addProducts']);

        $productsCollector->group('/{productId:[0-9a-z]+}', function (RouteCollectorProxy $productCollector) {
            $productCollector->get('', [ProductController::class, 'getProduct']);
            $productCollector->post('', [ProductController::class, 'updateProduct']);
            $productCollector->delete('', [ProductController::class, 'deleteProduct']);
            $productCollector->post('/sell', [ProductController::class, 'sellProduct']);
        });
    });
});

// TODO: Uncomment below
// $routeCollector = $app->getRouteCollector();
// $routeCollector->setCacheFile(sys_get_temp_dir() . '/routecache');

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
// TODO: remove below
$app->addErrorMiddleware(true, false, false);

try {
    $app->run();
} catch (HttpNotFoundException $e) {
    http_response_code(404);
}
