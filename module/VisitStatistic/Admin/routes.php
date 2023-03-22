<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'visit_statistic/config', 'ConfigController@index');

$router->match(['get', 'post'], 'visit_statistic/item', 'VisitStatisticItemController@index');
$router->match(['get', 'post'], 'visit_statistic/item/add', 'VisitStatisticItemController@add');
$router->match(['get', 'post'], 'visit_statistic/item/edit', 'VisitStatisticItemController@edit');
$router->match(['post'], 'visit_statistic/item/delete', 'VisitStatisticItemController@delete');
$router->match(['get'], 'visit_statistic/item/show', 'VisitStatisticItemController@show');
$router->match(['post'], 'visit_statistic/item/sort', 'VisitStatisticItemController@sort');
