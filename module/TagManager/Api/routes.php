<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'tag_manager/parse', 'TagManagerController@parse');

