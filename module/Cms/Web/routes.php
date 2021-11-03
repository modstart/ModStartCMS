<?php

$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'cms', 'IndexController@index');
    $router->match(['get', 'post'], 'a/{alias_url}', 'DetailController@index');
    $router->match(['get', 'post'], 'c/{id}', 'ListController@index');

    foreach (\Module\Cms\Util\CmsCatUtil::allSafely() as $item) {
        if (!empty($item['url'])) {
            $router->match(['get', 'post'], $item['url'], 'ListController@index');
        }
    }

});

