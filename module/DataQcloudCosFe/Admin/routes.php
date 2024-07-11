<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'data_qcloud_cos_fe/config', 'ConfigController@index');

