<?php


$router->match(['get', 'post'], 'cms/config/setting', 'ConfigController@setting');

$router->match(['get', 'post'], 'cms/post', 'PostController@index');
$router->match(['get', 'post'], 'cms/post/add', 'PostController@add');
$router->match(['get', 'post'], 'cms/post/edit', 'PostController@edit');
$router->match(['get', 'post'], 'cms/post/delete', 'PostController@delete');
$router->match(['get', 'post'], 'cms/post/show', 'PostController@show');

$router->match(['get', 'post'], 'cms/post_system', 'PostSystemController@index');
$router->match(['get', 'post'], 'cms/post_system/add', 'PostSystemController@add');
$router->match(['get', 'post'], 'cms/post_system/edit', 'PostSystemController@edit');
$router->match(['get', 'post'], 'cms/post_system/delete', 'PostSystemController@delete');
$router->match(['get', 'post'], 'cms/post_system/show', 'PostSystemController@show');

$router->match(['get', 'post'], 'cms/channel', 'ChannelController@index');
$router->match(['get', 'post'], 'cms/channel/add', 'ChannelController@add');
$router->match(['get', 'post'], 'cms/channel/edit', 'ChannelController@edit');
$router->match(['get', 'post'], 'cms/channel/delete', 'ChannelController@delete');
$router->match(['get', 'post'], 'cms/channel/show', 'ChannelController@show');
$router->match(['get', 'post'], 'cms/channel/sort', 'ChannelController@sort');
