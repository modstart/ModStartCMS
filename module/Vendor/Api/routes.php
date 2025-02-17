<?php
/* @var \Illuminate\Routing\Router $router */
$middlewares = [];
if (@class_exists(\Module\Member\Middleware\ApiAuthMiddleware::class)) {
    $middlewares[] = \Module\Member\Middleware\ApiAuthMiddleware::class;
}
$router->group([
    'middleware' => $middlewares,
], function () use ($router) {

    $router->match(['post'], 'captcha/image', 'CaptchaController@image');
    $router->match(['post'], 'entry/biz', 'EntryController@biz');

});
