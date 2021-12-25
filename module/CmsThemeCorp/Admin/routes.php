<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'cms_template_corp/config', 'ConfigController@index');
$router->match(['get', 'post'], 'cms_template_corp/config/fill_data', 'ConfigController@fillData');
