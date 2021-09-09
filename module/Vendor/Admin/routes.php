<?php


$router->match(['get', 'post'], 'widget/icon', 'WidgetIconController@index');
$router->match(['get', 'post'], 'widget/link_select', 'WidgetLinkController@select');

