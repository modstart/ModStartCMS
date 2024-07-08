<?php
/* @var \Illuminate\Routing\Router $router */
$router->group([
    'middleware' => [
        \Module\Member\Middleware\ApiAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['post'], 'cms_member_content/delete', 'MemberContentController@delete');

});




