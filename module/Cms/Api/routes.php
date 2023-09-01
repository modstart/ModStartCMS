<?php
/* @var \Illuminate\Routing\Router $router */
$router->group([
    'middleware' => [
        \Module\Member\Middleware\ApiAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['post'], 'cms/list', 'ListController@index');
    $router->match(['post'], 'cms/detail', 'DetailController@index');
    $router->match(['post'], 'cms/form', 'FormController@index');
    $router->match(['post'], 'cms/form/submit', 'FormController@submit');
    $router->match(['post'], 'cms/page', 'PageController@index');
    $router->match(['post'], 'cms/operate/like', 'OperateController@like');

});
