<?php
/* @var \Illuminate\Routing\Router $router */

$router->group([
    'middleware' => [
        \Module\Vendor\Middleware\StatelessRouteMiddleware::class
    ],
], function () use ($router) {

    $router->match(['get'], 'visit_statistic/tick', 'IndexController@index');

});




