<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'upgrade', 'UpgradeController@index');
$router->match(['get', 'post'], 'upgrade/info', 'UpgradeController@info');
$router->match(['get', 'post'], 'upgrade/auth', 'UpgradeController@auth');



