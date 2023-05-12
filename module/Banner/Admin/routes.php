<?php

/* @var \Illuminate\Routing\Router $router */
$router->match(['get', 'post'], 'banner', 'BannerController@index');
$router->match(['get', 'post'], 'banner/add', 'BannerController@add');
$router->match(['get', 'post'], 'banner/edit', 'BannerController@edit');
$router->match(['post'], 'banner/delete', 'BannerController@delete');
$router->match(['get'], 'banner/show', 'BannerController@show');
$router->match(['post'], 'banner/sort', 'BannerController@sort');
