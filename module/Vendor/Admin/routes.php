<?php
/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'widget/icon', 'WidgetIconController@index');
$router->match(['get', 'post'], 'widget/link_select', 'WidgetLinkController@select');

$router->match(['get', 'post'], 'security/second_verify', 'SecurityController@secondVerify');

