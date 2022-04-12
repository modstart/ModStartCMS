<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'cms_url_mix/config', 'ConfigController@index');

$router->match(['get', 'post'], 'cms_url_mix/category', 'CmsUrlMixCategoryController@index');
$router->match(['get', 'post'], 'cms_url_mix/category/add', 'CmsUrlMixCategoryController@add');
$router->match(['get', 'post'], 'cms_url_mix/category/edit', 'CmsUrlMixCategoryController@edit');
$router->match(['get', 'post'], 'cms_url_mix/category/delete', 'CmsUrlMixCategoryController@delete');
$router->match(['get', 'post'], 'cms_url_mix/category/show', 'CmsUrlMixCategoryController@show');
$router->match(['get', 'post'], 'cms_url_mix/category/sort', 'CmsUrlMixCategoryController@sort');

$router->match(['get', 'post'], 'cms_url_mix', 'CmsUrlMixController@index');
$router->match(['get', 'post'], 'cms_url_mix/add', 'CmsUrlMixController@add');
$router->match(['get', 'post'], 'cms_url_mix/edit', 'CmsUrlMixController@edit');
$router->match(['get', 'post'], 'cms_url_mix/delete', 'CmsUrlMixController@delete');
$router->match(['get', 'post'], 'cms_url_mix/show', 'CmsUrlMixController@show');
$router->match(['get', 'post'], 'cms_url_mix/sort', 'CmsUrlMixController@sort');
