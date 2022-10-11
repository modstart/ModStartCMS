<?php

/* @var \Illuminate\Routing\Router $router */
$router->match(['get', 'post'], 'partner', '\Module\Partner\Admin\Controller\PartnerController@index');
$router->match(['get', 'post'], 'partner/add', '\Module\Partner\Admin\Controller\PartnerController@add');
$router->match(['get', 'post'], 'partner/edit', '\Module\Partner\Admin\Controller\PartnerController@edit');
$router->match(['post'], 'partner/delete', '\Module\Partner\Admin\Controller\PartnerController@delete');
$router->match(['get'], 'partner/show', '\Module\Partner\Admin\Controller\PartnerController@show');
$router->match(['post'], 'partner/sort', '\Module\Partner\Admin\Controller\PartnerController@sort');
