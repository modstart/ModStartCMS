<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'content_block/config', 'ConfigController@index');

$router->match(['get', 'post'], 'content_block/category', 'ContentBlockCategoryController@index');
$router->match(['get', 'post'], 'content_block/category/add', 'ContentBlockCategoryController@add');
$router->match(['get', 'post'], 'content_block/category/edit', 'ContentBlockCategoryController@edit');
$router->match(['post'], 'content_block/category/delete', 'ContentBlockCategoryController@delete');
$router->match(['get'], 'content_block/category/show', 'ContentBlockCategoryController@show');
$router->match(['post'], 'content_block/category/sort', 'ContentBlockCategoryController@sort');

$router->match(['get', 'post'], 'content_block', 'ContentBlockController@index');
$router->match(['get', 'post'], 'content_block/add', 'ContentBlockController@add');
$router->match(['get', 'post'], 'content_block/edit', 'ContentBlockController@edit');
$router->match(['post'], 'content_block/delete', 'ContentBlockController@delete');
$router->match(['get'], 'content_block/show', 'ContentBlockController@show');
$router->match(['post'], 'content_block/sort', 'ContentBlockController@sort');
