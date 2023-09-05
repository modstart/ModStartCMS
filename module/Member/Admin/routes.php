<?php

/* @var \Illuminate\Routing\Router $router */

$router->match(['get', 'post'], 'member/config/setting', 'ConfigController@setting');
$router->match(['get', 'post'], 'member/config/agreement', 'ConfigController@agreement');
$router->match(['get', 'post'], 'member/config/money', 'ConfigController@money');
$router->match(['get', 'post'], 'member/config/credit', 'ConfigController@credit');
$router->match(['get', 'post'], 'member/config/message', 'ConfigController@message');

$router->match(['get'], 'member/config/param', 'ConfigController@param');

$router->match(['post'], 'member/config/data_statistic', 'ConfigController@dataStatistic');

$router->match(['get', 'post'], 'member/dashboard', 'MemberDashboardController@index');

$router->match(['get', 'post'], 'member', 'MemberController@index');
$router->match(['get', 'post'], 'member/add', 'MemberController@add');
$router->match(['get', 'post'], 'member/edit', 'MemberController@edit');
$router->match(['post'], 'member/delete', 'MemberController@delete');
$router->match(['get'], 'member/show', 'MemberController@show');
$router->match(['get', 'post'], 'member/select', 'MemberController@select');
$router->match(['get', 'post'], 'member/search', 'MemberController@search');
$router->match(['get', 'post'], 'member/select_remote', 'MemberController@selectRemote');
$router->match(['get', 'post'], 'member/reset_password', 'MemberController@resetPassword');
$router->match(['get', 'post'], 'member/send_message', 'MemberController@sendMessage');
$router->match(['get', 'post'], 'member/status_forbidden', 'MemberController@statusForbidden');
$router->match(['get', 'post'], 'member/export', 'MemberController@export');

$router->match(['get', 'post'], 'member_vip_set', 'MemberVipSetController@index');
$router->match(['get', 'post'], 'member_vip_set/add', 'MemberVipSetController@add');
$router->match(['get', 'post'], 'member_vip_set/edit', 'MemberVipSetController@edit');
$router->match(['post'], 'member_vip_set/delete', 'MemberVipSetController@delete');
$router->match(['get'], 'member_vip_set/show', 'MemberVipSetController@show');
$router->match(['post'], 'member_vip_set/sort', 'MemberVipSetController@sort');

$router->match(['get', 'post'], 'member_vip_set/config', 'MemberVipSetController@config');

$router->match(['get', 'post'], 'member_vip_right', 'MemberVipRightController@index');
$router->match(['get', 'post'], 'member_vip_right/add', 'MemberVipRightController@add');
$router->match(['get', 'post'], 'member_vip_right/edit', 'MemberVipRightController@edit');
$router->match(['post'], 'member_vip_right/delete', 'MemberVipRightController@delete');
$router->match(['get'], 'member_vip_right/show', 'MemberVipRightController@show');
$router->match(['post'], 'member_vip_right/sort', 'MemberVipRightController@sort');

$router->match(['get', 'post'], 'member_vip_order', 'MemberVipOrderController@index');
$router->match(['get', 'post'], 'member_vip_order/add', 'MemberVipOrderController@add');
$router->match(['get', 'post'], 'member_vip_order/edit', 'MemberVipOrderController@edit');
$router->match(['post'], 'member_vip_order/delete', 'MemberVipOrderController@delete');
$router->match(['get'], 'member_vip_order/show', 'MemberVipOrderController@show');

$router->match(['get', 'post'], 'member/money_charge_order', 'MemberMoneyChargeOrderController@index');
$router->match(['get', 'post'], 'member/money_charge_order/add', 'MemberMoneyChargeOrderController@add');
$router->match(['get', 'post'], 'member/money_charge_order/edit', 'MemberMoneyChargeOrderController@edit');
$router->match(['post'], 'member/money_charge_order/delete', 'MemberMoneyChargeOrderController@delete');
$router->match(['get'], 'member/money_charge_order/show', 'MemberMoneyChargeOrderController@show');

$router->match(['get', 'post'], 'member_money_log', 'MemberMoneyLogController@index');
$router->match(['get', 'post'], 'member_money_log/add', 'MemberMoneyLogController@add');
$router->match(['get', 'post'], 'member_money_log/edit', 'MemberMoneyLogController@edit');
$router->match(['post'], 'member_money_log/delete', 'MemberMoneyLogController@delete');
$router->match(['get'], 'member_money_log/show', 'MemberMoneyLogController@show');

$router->match(['get', 'post'], 'member_credit_log', 'MemberCreditLogController@index');
$router->match(['get', 'post'], 'member_credit_log/add', 'MemberCreditLogController@add');
$router->match(['get', 'post'], 'member_credit_log/edit', 'MemberCreditLogController@edit');
$router->match(['post'], 'member_credit_log/delete', 'MemberCreditLogController@delete');
$router->match(['get'], 'member_credit_log/show', 'MemberCreditLogController@show');

$router->match(['get', 'post'], 'member_money_cash', 'MemberMoneyCashController@index');
$router->match(['get', 'post'], 'member_money_cash/add', 'MemberMoneyCashController@add');
$router->match(['get', 'post'], 'member_money_cash/edit', 'MemberMoneyCashController@edit');
$router->match(['post'], 'member_money_cash/delete', 'MemberMoneyCashController@delete');
$router->match(['get'], 'member_money_cash/show', 'MemberMoneyCashController@show');
$router->match(['get', 'post'], 'member_money_cash/pass', 'MemberMoneyCashController@pass');

$router->match(['get', 'post'], 'member_group', 'MemberGroupController@index');
$router->match(['get', 'post'], 'member_group/add', 'MemberGroupController@add');
$router->match(['get', 'post'], 'member_group/edit', 'MemberGroupController@edit');
$router->match(['post'], 'member_group/delete', 'MemberGroupController@delete');
$router->match(['get'], 'member_group/show', 'MemberGroupController@show');
$router->match(['post'], 'member_group/sort', 'MemberGroupController@sort');

$router->match(['get', 'post'], 'member_credit/charge', 'MemberCreditController@charge');

$router->match(['get', 'post'], 'member_money/charge', 'MemberMoneyController@charge');
