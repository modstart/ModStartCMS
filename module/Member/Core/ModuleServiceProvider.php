<?php

namespace Module\Member\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ColorUtil;
use ModStart\Layout\Row;
use ModStart\Module\ModuleClassLoader;
use ModStart\Module\ModuleManager;
use Module\Member\Config\MemberMenu;
use Module\Member\Listener\MemberVipPayListener;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;
use Module\Vendor\Admin\Config\AdminWidgetLink;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        MemberMenu::register(function () {
            return [
                [
                    'icon' => 'details',
                    'title' => '资产',
                    'sort' => 900,
                    'children' => [
                        ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') && modstart_config('Member_MoneyEnable', false) ? [
                            'title' => '我的钱包',
                            'url' => modstart_web_url('member_money'),
                        ] : null,
                        ModuleManager::getModuleConfigBoolean('Member', 'creditEnable') && modstart_config('Member_CreditEnable', false) ? [
                            'title' => '我的积分',
                            'url' => modstart_web_url('member_credit'),
                        ] : null,
                    ],
                ],
                [
                    'icon' => 'user',
                    'title' => '我的',
                    'sort' => 1000,
                    'children' => [
                        ModuleManager::getModuleConfigBoolean('Member', 'addressEnable') ? [
                            'title' => '我的地址',
                            'url' => modstart_web_url('member_address'),
                        ] : null,
                        [
                            'title' => '修改密码',
                            'url' => modstart_web_url('member_profile/password'),
                        ],
                        [
                            'title' => '修改头像',
                            'url' => modstart_web_url('member_profile/avatar'),
                        ],
                        [
                            'title' => '绑定手机',
                            'url' => modstart_web_url('member_profile/phone'),
                        ],
                        [
                            'title' => '绑定邮箱',
                            'url' => modstart_web_url('member_profile/email'),
                        ],
                    ]
                ],
            ];
        });

        AdminWidgetDashboard::registerIcon(function (Row $row) {
            $row->column(3, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-user', ModelUtil::count('member_user'), '用户数',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
        });

        AdminWidgetLink::register(function () {
            return AdminWidgetLink::build('会员', [
                ['注册', modstart_web_url('register')],
                ['登录', modstart_web_url('login')],
                ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ? ['用户钱包', modstart_web_url('member_money')] : null,
                ModuleManager::getModuleConfigBoolean('Member', 'creditEnable') ? ['用户积分', modstart_web_url('login')] : null,
            ]);
        });

        $events->subscribe(MemberVipPayListener::class);

        AdminMenu::register(function () {
            return [
                [
                    'title' => '用户中心',
                    'icon' => 'users',
                    'sort' => 100,
                    'children' => [
                        [
                            'title' => '用户统计',
                            'url' => '\Module\Member\Admin\Controller\MemberDashboardController@index',
                        ],
                        [
                            'title' => '用户管理',
                            'url' => '\Module\Member\Admin\Controller\MemberController@index',
                        ],
                        ModuleManager::getModuleConfigBoolean('Member', 'vipEnable') ?
                            [
                                'title' => '会员VIP订单',
                                'url' => '\Module\Member\Admin\Controller\MemberVipOrderController@index',
                            ] : null,
                        ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ?
                            [
                                'title' => '资金提现申请',
                                'url' => '\Module\Member\Admin\Controller\MemberMoneyCashController@index',
                            ] : null,
                    ]
                ],
                [
                    'title' => '功能设置',
                    'icon' => 'tools',
                    'sort' => 300,
                    'children' => [
                        [
                            'title' => '用户设置',
                            'children' => [
                                [
                                    'title' => '功能设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@setting',
                                ],
                                [
                                    'title' => '用户协议',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@agreement',
                                ],
                                ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ? [
                                    'title' => '资金设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@money',
                                ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'creditEnable') ? [
                                    'title' => '积分设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@credit',
                                ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'vipEnable') ? [
                                    'title' => 'VIP设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@vip',
                                ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'vipEnable') ? [
                                    'title' => 'VIP等级',
                                    'url' => '\Module\Member\Admin\Controller\MemberVipSetController@index',
                                ] : null,
                            ]
                        ],
                    ]
                ],
            ];
        });
    }

    
    public function register()
    {

    }
}
