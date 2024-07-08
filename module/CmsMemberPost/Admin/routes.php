<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'cms_member_post/config', 'ConfigController@index');

$router->match(['get', 'post'], 'cms_member_post/category', 'CmsMemberPostCategoryController@index');
$router->match(['get', 'post'], 'cms_member_post/category/add', 'CmsMemberPostCategoryController@add');
$router->match(['get', 'post'], 'cms_member_post/category/edit', 'CmsMemberPostCategoryController@edit');
$router->match(['post'], 'cms_member_post/category/delete', 'CmsMemberPostCategoryController@delete');
$router->match(['get'], 'cms_member_post/category/show', 'CmsMemberPostCategoryController@show');
$router->match(['post'], 'cms_member_post/category/sort', 'CmsMemberPostCategoryController@sort');

$router->match(['get', 'post'], 'cms_member_post', 'CmsMemberPostController@index');
$router->match(['get', 'post'], 'cms_member_post/add', 'CmsMemberPostController@add');
$router->match(['get', 'post'], 'cms_member_post/edit', 'CmsMemberPostController@edit');
$router->match(['post'], 'cms_member_post/delete', 'CmsMemberPostController@delete');
$router->match(['get'], 'cms_member_post/show', 'CmsMemberPostController@show');
$router->match(['post'], 'cms_member_post/sort', 'CmsMemberPostController@sort');
