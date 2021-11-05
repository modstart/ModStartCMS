<?php

$router->group([
    'middleware' => [
        \Module\Member\Middleware\ApiAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'oauth/login_wechat_mini_program', 'AuthController@loginWechatMiniProgram');

});
