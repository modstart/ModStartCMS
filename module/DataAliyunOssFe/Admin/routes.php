<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'data_aliyun_oss_fe/config', 'ConfigController@index');

