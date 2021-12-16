<?php

/* @var \Illuminate\Routing\Router $router */
$router->match(['get', 'post'], 'banner', '\Module\Banner\Admin\Controller\BannerController@index');
$router->match(['get', 'post'], 'banner/add', '\Module\Banner\Admin\Controller\BannerController@add');
$router->match(['get', 'post'], 'banner/edit', '\Module\Banner\Admin\Controller\BannerController@edit');
$router->match(['get', 'post'], 'banner/delete', '\Module\Banner\Admin\Controller\BannerController@delete');
$router->match(['get', 'post'], 'banner/show', '\Module\Banner\Admin\Controller\BannerController@show');
$router->match(['get', 'post'], 'banner/sort', '\Module\Banner\Admin\Controller\BannerController@sort');
