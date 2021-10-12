<?php



$router->match(['get', 'post'], 'upgrade', 'UpgradeController@index');
$router->match(['get', 'post'], 'upgrade/info', 'UpgradeController@info');



