<?php
/* @var \Illuminate\Routing\Router $router */

$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'tag_manager', 'IndexController@index');

});




