<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'captcha_tecmz', 'IndexController@index');

$router->match(['get', 'post'], 'captcha_tecmz/config/setting', 'ConfigController@setting');



