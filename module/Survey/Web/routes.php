<?php
/* @var \Illuminate\Routing\Router $router */

$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'survey/activity/{alias}', 'ActivityController@index');

});




