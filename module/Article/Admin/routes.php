<?php

/* @var \Illuminate\Routing\Router $router */
$router->match(['get', 'post'], 'article', 'ArticleController@index');
$router->match(['get', 'post'], 'article/add', 'ArticleController@add');
$router->match(['get', 'post'], 'article/edit', 'ArticleController@edit');
$router->match(['post'], 'article/delete', 'ArticleController@delete');
$router->match(['get'], 'article/show', 'ArticleController@show');
