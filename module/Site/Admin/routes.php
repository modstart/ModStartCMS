<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'site/config/setting', 'ConfigController@setting');
$router->match(['get', 'post'], 'site/config/config', 'ConfigController@config');



