<?php
/* @var \Illuminate\Routing\Router $router */
$middlewares = [];
if (class_exists(\Module\Member\Middleware\WebAuthMiddleware::class)) {
    $middlewares[] = \Module\Member\Middleware\WebAuthMiddleware::class;
}
$router->group([
    'middleware' => $middlewares,
], function () use ($router) {

    $router->match(['get'], 'site/contact', 'SiteController@contact');

});


