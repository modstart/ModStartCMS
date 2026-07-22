<?php
/* @var \Illuminate\Routing\Router $router */
$middlewares = [];
if (file_exists(base_path('module/Member/Middleware/WebAuthMiddleware.php'))) {
    $middlewares[] = \Module\Member\Middleware\WebAuthMiddleware::class;
}
$router->group([
    'middleware' => $middlewares,
], function () use ($router) {

    $router->match(['get'], 'aigc/distribution', 'DistributionController@index');

});

