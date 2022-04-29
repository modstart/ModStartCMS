<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'tag_manager', 'TagManagerController@index');
$router->match(['get', 'post'], 'tag_manager/add', 'TagManagerController@add');
$router->match(['get', 'post'], 'tag_manager/edit', 'TagManagerController@edit');
$router->match(['get', 'post'], 'tag_manager/delete', 'TagManagerController@delete');
$router->match(['get', 'post'], 'tag_manager/show', 'TagManagerController@show');
$router->match(['get', 'post'], 'tag_manager/sort', 'TagManagerController@sort');

$router->match(['get', 'post'], 'tag_manager/build', 'TagManagerBuildController@index');
