<?php

$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'cms_writer', 'IndexController@index');

    $router->match(['get', 'post'], 'writer', 'WriterController@index');
    $router->match(['get', 'post'], 'writer/setting', 'WriterController@setting');

    $router->match(['get', 'post'], 'writer/category', 'WriterController@category');
    $router->match(['get', 'post'], 'writer/category_edit', 'WriterController@categoryEdit');
    $router->match(['get', 'post'], 'writer/category_delete', 'WriterController@categoryDelete');

    $router->match(['get', 'post'], 'writer/post', 'WriterController@post');
    $router->match(['get', 'post'], 'writer/post_edit', 'WriterController@postEdit');

    $router->match(['get', 'post'], 'channel/{alias_url}', 'ChannelController@index');
    $router->match(['get', 'post'], 'p/{alias}', 'PostController@show');

});

