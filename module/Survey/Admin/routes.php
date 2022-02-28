<?php

/* @var \Illuminate\Routing\Router $router */

//$router->match(['get', 'post'], 'survey/config', 'ConfigController@index');

$router->match(['get', 'post'], 'survey/answer', 'AnswerController@index');
$router->match(['get', 'post'], 'survey/answer/add', 'AnswerController@add');
$router->match(['get', 'post'], 'survey/answer/edit', 'AnswerController@edit');
$router->match(['get', 'post'], 'survey/answer/delete', 'AnswerController@delete');
$router->match(['get', 'post'], 'survey/answer/show', 'AnswerController@show');

$router->match(['get', 'post'], 'survey/activity', 'ActivityController@index');
$router->match(['get', 'post'], 'survey/activity/add', 'ActivityController@add');
$router->match(['get', 'post'], 'survey/activity/edit', 'ActivityController@edit');
$router->match(['get', 'post'], 'survey/activity/delete', 'ActivityController@delete');
$router->match(['get', 'post'], 'survey/activity/show', 'ActivityController@show');
