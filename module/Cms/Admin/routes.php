<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'cms/config/setting', 'ConfigController@setting');

$router->match(['get', 'post'], 'cms/model', 'ModelController@index');
$router->match(['get', 'post'], 'cms/model/edit', 'ModelController@edit');
$router->match(['post'], 'cms/model/delete', 'ModelController@delete');
$router->match(['get', 'post'], 'cms/model/field/{modelId}', 'ModelController@field');
$router->match(['get', 'post'], 'cms/model/field/{modelId}/edit', 'ModelController@fieldEdit');
$router->match(['post'], 'cms/model/field/{modelId}/delete', 'ModelController@fieldDelete');
$router->match(['post'], 'cms/model/field/{modelId}/sort', 'ModelController@fieldSort');

$router->match(['get', 'post'], 'cms/template', 'TemplateController@index');

$router->match(['get', 'post'], 'cms/cat', 'CatController@index');
$router->match(['get', 'post'], 'cms/cat/add', 'CatController@add');
$router->match(['get', 'post'], 'cms/cat/edit', 'CatController@edit');
$router->match(['post'], 'cms/cat/delete', 'CatController@delete');
$router->match(['get'], 'cms/cat/show', 'CatController@show');
$router->match(['post'], 'cms/cat/sort', 'CatController@sort');

$router->match(['get', 'post'], 'cms/content/{modelId}', 'ContentController@index');
$router->match(['get', 'post'], 'cms/content/edit/{modelId}', 'ContentController@edit');
$router->match(['post'], 'cms/content/delete/{modelId}', 'ContentController@delete');
$router->match(['get', 'post'], 'cms/content/batch_move/{modelId}', 'ContentController@batchMove');

$router->match(['get', 'post'], 'cms/backup', 'BackupController@index');
$router->match(['get', 'post'], 'cms/restore', 'RestoreController@index');
$router->match(['post'], 'cms/restore/delete', 'RestoreController@delete');
$router->match(['post'], 'cms/restore/submit', 'RestoreController@submit');
