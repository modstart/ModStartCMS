<?php
/* @var \Illuminate\Routing\Router $router */
$middleware = [];
if (class_exists(\Module\Member\Middleware\WebAuthMiddleware::class)) {
    $middleware[] = \Module\Member\Middleware\WebAuthMiddleware::class;
}
$router->group([
    'middleware' => $middleware,
], function () use ($router) {
    $router->match(['get'], 'partner', 'PartnerController@index');
});

