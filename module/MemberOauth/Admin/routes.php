<?php



$router->match(['get', 'post'], 'member_oauth/config/wechat_mobile', 'ConfigController@wechatMobile');
$router->match(['get', 'post'], 'member_oauth/config/wechat', 'ConfigController@wechat');
$router->match(['get', 'post'], 'member_oauth/config/qq', 'ConfigController@qq');
$router->match(['get', 'post'], 'member_oauth/config/weibo', 'ConfigController@weibo');
$router->match(['get', 'post'], 'member_oauth/config/wechat_mini_program', 'ConfigController@wechatMiniProgram');
