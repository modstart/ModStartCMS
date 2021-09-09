<?php


$router->match(['get', 'post'], 'ad', '\Module\Ad\Admin\Controller\AdController@index');
$router->match(['get', 'post'], 'ad/add', '\Module\Ad\Admin\Controller\AdController@add');
$router->match(['get', 'post'], 'ad/edit', '\Module\Ad\Admin\Controller\AdController@edit');
$router->match(['get', 'post'], 'ad/delete', '\Module\Ad\Admin\Controller\AdController@delete');
$router->match(['get', 'post'], 'ad/show', '\Module\Ad\Admin\Controller\AdController@show');
$router->match(['get', 'post'], 'ad/sort', '\Module\Ad\Admin\Controller\AdController@sort');
