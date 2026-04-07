<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'site_counter/config/setting', 'ConfigController@setting');



