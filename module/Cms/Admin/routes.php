<?php


$router->match(['get', 'post'], 'cms/model', 'ModelController@index');
$router->match(['get', 'post'], 'cms/model/edit', 'ModelController@edit');
$router->match(['get', 'post'], 'cms/model/delete', 'ModelController@delete');
$router->match(['get', 'post'], 'cms/model/field/{modelId}', 'ModelController@field');
$router->match(['get', 'post'], 'cms/model/field/{modelId}/edit', 'ModelController@fieldEdit');
$router->match(['get', 'post'], 'cms/model/field/{modelId}/delete', 'ModelController@fieldDelete');

$router->match(['get', 'post'], 'cms/template', 'TemplateController@index');

$router->match(['get', 'post'], 'cms/cat', 'CatController@index');
$router->match(['get', 'post'], 'cms/cat/add', 'CatController@add');
$router->match(['get', 'post'], 'cms/cat/edit', 'CatController@edit');
$router->match(['get', 'post'], 'cms/cat/delete', 'CatController@delete');
$router->match(['get', 'post'], 'cms/cat/show', 'CatController@show');
$router->match(['get', 'post'], 'cms/cat/sort', 'CatController@sort');

$router->match(['get', 'post'], 'cms/content/{modelId}', 'ContentController@index');
$router->match(['get', 'post'], 'cms/content/edit/{modelId}', 'ContentController@edit');
$router->match(['get', 'post'], 'cms/content/delete/{modelId}', 'ContentController@delete');
