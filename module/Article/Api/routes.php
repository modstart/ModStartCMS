<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'article/get', 'ArticleController@get');

