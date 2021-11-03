<?php

$router->group([
    'middleware' => [
        \Module\Member\Middleware\ApiAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'writer/setting/get', 'WriterController@settingGet');
    $router->match(['get', 'post'], 'writer/setting/save', 'WriterController@settingSave');

    $router->match(['get', 'post'], 'writer/category/all', 'WriterController@categoryAll');

    $router->match(['get', 'post'], 'writer/post/paginate', 'WriterController@postPaginate');
    $router->match(['get', 'post'], 'writer/post/get', 'WriterController@postGet');
    $router->match(['get', 'post'], 'writer/post/edit', 'WriterController@postEdit');
    $router->match(['get', 'post'], 'writer/post/delete', 'WriterController@postDelete');
    $router->match(['get', 'post'], 'writer/post/publish', 'WriterController@postPublish');
    $router->match(['get', 'post'], 'writer/post/publish_cancel', 'WriterController@postPublishCancel');

    $router->match(['get', 'post'], 'post/get', 'PostController@get');
    $router->match(['get', 'post'], 'post/like', 'PostController@like');
    $router->match(['get', 'post'], 'post/unlike', 'PostController@unlike');

});
