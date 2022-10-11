<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'nav', 'NavController@index');
$router->match(['get', 'post'], 'nav/add', 'NavController@add');
$router->match(['get', 'post'], 'nav/edit', 'NavController@edit');
$router->match(['post'], 'nav/delete', 'NavController@delete');
$router->match(['get'], 'nav/show', 'NavController@show');
$router->match(['post'], 'nav/sort', 'NavController@sort');
