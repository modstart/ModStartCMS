<?php



$router->match(['get', 'post'], 'member/config/setting', 'ConfigController@setting');
$router->match(['get', 'post'], 'member/config/agreement', 'ConfigController@agreement');
$router->match(['get', 'post'], 'member/config/vip', 'ConfigController@vip');
$router->match(['get', 'post'], 'member/config/money', 'ConfigController@money');
$router->match(['get', 'post'], 'member/config/credit', 'ConfigController@credit');

$router->match(['get', 'post'], 'member/dashboard', 'MemberDashboardController@index');

$router->match(['get', 'post'], 'member', 'MemberController@index');
$router->match(['get', 'post'], 'member/add', 'MemberController@add');
$router->match(['get', 'post'], 'member/edit', 'MemberController@edit');
$router->match(['get', 'post'], 'member/delete', 'MemberController@delete');
$router->match(['get', 'post'], 'member/show', 'MemberController@show');
$router->match(['get', 'post'], 'member/select', 'MemberController@select');
$router->match(['get', 'post'], 'member/search', 'MemberController@search');

$router->match(['get', 'post'], 'member_vip_set', 'MemberVipSetController@index');
$router->match(['get', 'post'], 'member_vip_set/add', 'MemberVipSetController@add');
$router->match(['get', 'post'], 'member_vip_set/edit', 'MemberVipSetController@edit');
$router->match(['get', 'post'], 'member_vip_set/delete', 'MemberVipSetController@delete');
$router->match(['get', 'post'], 'member_vip_set/show', 'MemberVipSetController@show');
$router->match(['get', 'post'], 'member_vip_set/sort', 'MemberVipSetController@sort');

$router->match(['get', 'post'], 'member_vip_order', 'MemberVipOrderController@index');
$router->match(['get', 'post'], 'member_vip_order/add', 'MemberVipOrderController@add');
$router->match(['get', 'post'], 'member_vip_order/edit', 'MemberVipOrderController@edit');
$router->match(['get', 'post'], 'member_vip_order/delete', 'MemberVipOrderController@delete');
$router->match(['get', 'post'], 'member_vip_order/show', 'MemberVipOrderController@show');

$router->match(['get', 'post'], 'member_money_cash', 'MemberMoneyCashController@index');
$router->match(['get', 'post'], 'member_money_cash/add', 'MemberMoneyCashController@add');
$router->match(['get', 'post'], 'member_money_cash/edit', 'MemberMoneyCashController@edit');
$router->match(['get', 'post'], 'member_money_cash/delete', 'MemberMoneyCashController@delete');
$router->match(['get', 'post'], 'member_money_cash/show', 'MemberMoneyCashController@show');
$router->match(['get', 'post'], 'member_money_cash/pass', 'MemberMoneyCashController@pass');

