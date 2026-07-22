<?php
/* @var \Illuminate\Routing\Router $router */
$middleware = [];
if (file_exists(base_path('module/Member/Middleware/WebAuthMiddleware.php'))) {
    $middleware[] = \Module\Member\Middleware\WebAuthMiddleware::class;
}
$router->group([
    'middleware' => $middleware,
], function () use ($router) {
    $router->match(['get'], 'partner', 'PartnerController@index');
});

