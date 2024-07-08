<?php
/* @var \Illuminate\Routing\Router $router */

$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'cms_member_content', 'MemberContentController@index');
    $router->match(['get', 'post'], 'cms_member_content/edit', 'MemberContentController@edit');


});




