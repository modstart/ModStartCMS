<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'aigc/key_pool', 'AigcKeyPoolController@index');
$router->match(['get', 'post'], 'aigc/key_pool/add', 'AigcKeyPoolController@add');
$router->match(['get', 'post'], 'aigc/key_pool/edit', 'AigcKeyPoolController@edit');
$router->match(['post'], 'aigc/key_pool/delete', 'AigcKeyPoolController@delete');
$router->match(['get'], 'aigc/key_pool/show', 'AigcKeyPoolController@show');

$router->match(['get', 'post'], 'aigc/key_pool/config', 'AigcKeyPoolController@config');

$router->match(['post'], 'aigc/chat/{type}', 'AigcChatController@index');
