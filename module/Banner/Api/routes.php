<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'banner/get', 'BannerController@get');

