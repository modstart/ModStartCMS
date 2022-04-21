<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'module_store', 'ModuleStoreController@index');
$router->match(['post'], 'module_store/all', 'ModuleStoreController@all');
$router->match(['post'], 'module_store/install', 'ModuleStoreController@install');
$router->match(['post'], 'module_store/uninstall', 'ModuleStoreController@uninstall');
$router->match(['post'], 'module_store/enable', 'ModuleStoreController@enable');
$router->match(['post'], 'module_store/disable', 'ModuleStoreController@disable');
$router->match(['post'], 'module_store/upgrade', 'ModuleStoreController@upgrade');
$router->match(['get', 'post'], 'module_store/config/{module}', 'ModuleStoreController@config');
