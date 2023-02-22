<?php

/* @var \Illuminate\Routing\Router $router */
$router->match(['get', 'post'], 'partner', 'PartnerController@index');
$router->match(['get', 'post'], 'partner/add', 'PartnerController@add');
$router->match(['get', 'post'], 'partner/edit', 'PartnerController@edit');
$router->match(['post'], 'partner/delete', 'PartnerController@delete');
$router->match(['get'], 'partner/show', 'PartnerController@show');
$router->match(['post'], 'partner/sort', 'PartnerController@sort');
$router->match(['get', 'post'], 'partner/config', 'PartnerController@config');
