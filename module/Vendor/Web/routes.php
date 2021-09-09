<?php



$router->match(['get', 'post'], 'install/ping', 'InstallController@ping');
$router->match(['get', 'post'], 'install/prepare', 'InstallController@prepare');
$router->match(['get', 'post'], 'install/execute', 'InstallController@execute');
$router->match(['get', 'post'], 'install/lock', 'InstallController@lock');

$router->match(['get'], 'placeholder/{width}x{height}', '\Module\Vendor\Placeholder\PlaceholderController@index');