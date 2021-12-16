<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'article/{id}', 'ArticleController@views');
$router->match(['get', 'post'], 'article/{alias_url}', 'ArticleController@views');

