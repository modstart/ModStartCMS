<?php



$router->match(['get', 'post'], 'nav', 'NavController@index');
$router->match(['get', 'post'], 'nav/add', 'NavController@add');
$router->match(['get', 'post'], 'nav/edit', 'NavController@edit');
$router->match(['get', 'post'], 'nav/delete', 'NavController@delete');
$router->match(['get', 'post'], 'nav/show', 'NavController@show');
$router->match(['get', 'post'], 'nav/sort', 'NavController@sort');
