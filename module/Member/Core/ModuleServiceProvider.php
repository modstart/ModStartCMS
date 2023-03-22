<?php

namespace Module\Member\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ColorUtil;
use ModStart\Layout\Row;
use ModStart\Module\ModuleManager;
use Module\Member\Auth\MemberUser;
use Module\Member\Config\MemberHomeIcon;
use Module\Member\Config\MemberMenu;
use Module\Member\Provider\MemberDeleteScheduleProvider;
use Module\Member\Provider\VerifySmsTemplateProvider;
use Module\Member\Util\MemberCreditUtil;
use Module\Member\Util\MemberMoneyUtil;
use Module\PayCenter\Biz\PayCenterBiz;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;
use Module\Vendor\Admin\Config\AdminWidgetLink;
use Module\Vendor\Provider\Schedule\ScheduleProvider;
use Module\Vendor\Provider\SmsTemplate\SmsTemplateProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        MemberMenu::register(function () {
            return [
                [
                    'icon' => 'details',
                    'title' => '资产',
                    'sort' => 900,
                    'children' => [
                        ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ? [
                            'title' => '我的钱包',
                            'url' => modstart_web_url('member_money'),
                        ] : null,
                        ModuleManager::getModuleConfigBoolean('Member', 'creditEnable') ? [
                            'title' => '我的' . ModuleManager::getModuleConfig('Member', 'creditName', '积分'),
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
                            'icon' => 'iconfont icon-comment',
                            'title' => '我的消息',
                            'url' => modstart_web_url('member_message'),
                        ],
                        [
                            'title' => '账号安全',
                            'url' => modstart_web_url('member_profile/security'),
                        ],
                        [
                            'title' => '账号资料',
                            'url' => modstart_web_url('member_profile/profile'),
                        ],
                        [
                            'sort' => 999999,
                            'title' => '退出登录',
                            'url' => modstart_web_url('logout'),
                        ],
                    ]
                ],
            ];
        });

        SmsTemplateProvider::register(VerifySmsTemplateProvider::class);
        ScheduleProvider::register(MemberDeleteScheduleProvider::class);

        MemberHomeIcon::register(function () {
            return [
                [
                    'title' => '我的',
                    'sort' => 1000,
                    'children' => [
                        ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ? [
                            'icon' => 'iconfont icon-pay',
                            'value' => sprintf('￥%.2f', MemberMoneyUtil::getTotal(MemberUser::id())),
                            'title' => '我的钱包',
                            'url' => modstart_web_url('member_money'),
                        ] : null,
                        ModuleManager::getModuleConfigBoolean('Member', 'creditEnable') ? [
                            'icon' => 'iconfont icon-credit',
                            'value' => MemberCreditUtil::getTotal(MemberUser::id()),
                            'title' => '我的' . ModuleManager::getModuleConfig('Member', 'creditName', '积分'),
                            'url' => modstart_web_url('member_credit'),
                        ] : null,
                        [
                            'icon' => 'iconfont icon-comment',
                            'title' => '我的消息',
                            'url' => modstart_web_url('member_message'),
                        ],
                        [
                            'icon' => 'iconfont icon-card',
                            'title' => '我的资料',
                            'url' => modstart_web_url('member_profile'),
                        ],
                        [
                            'icon' => 'iconfont icon-lock',
                            'title' => '修改密码',
                            'url' => modstart_web_url('member_profile/password'),
                        ],
                        [
                            'icon' => 'iconfont icon-user',
                            'title' => '修改头像',
                            'url' => modstart_web_url('member_profile/avatar'),
                        ],
                        [
                            'icon' => 'iconfont icon-lock',
                            'title' => '退出登录',
                            'url' => modstart_web_url('logout'),
                        ],
                    ]
                ],
            ];
        });

        AdminWidgetDashboard::registerIcon(function (Row $row) {
            $row->column(3, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-user',
                ModelUtil::count('member_user', ['isDeleted' => false]),
                '用户',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
        });

        AdminWidgetLink::register(function () {
            return AdminWidgetLink::build('会员', [
                ['注册', modstart_web_url('register')],
                ['登录', modstart_web_url('login')],
                ['找回密码', modstart_web_url('retrieve')],
                ModuleManager::getModuleConfigBoolean('Member', 'vipEnable') ? ['开通VIP', modstart_web_url('member_vip')] : null,
                ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ? ['用户钱包', modstart_web_url('member_money')] : null,
                ModuleManager::getModuleConfigBoolean('Member', 'creditEnable') ? ['用户积分', modstart_web_url('login')] : null,
            ]);
        });

        if (modstart_module_enabled('PayCenter')) {
            PayCenterBiz::register(MemberMoneyChargePayCenterBiz::class);
            PayCenterBiz::register(MemberVipPayCenterBiz::class);
        }

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
                        [
                            'title' => '用户资产',
                            'children' => [
                                ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ?
                                    [
                                        'title' => '用户钱包流水',
                                        'url' => '\Module\Member\Admin\Controller\MemberMoneyLogController@index',
                                    ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ?
                                    [
                                        'title' => '用户钱包提现申请',
                                        'url' => '\Module\Member\Admin\Controller\MemberMoneyCashController@index',
                                    ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'creditEnable') ?
                                    [
                                        'title' => '用户积分流水',
                                        'url' => '\Module\Member\Admin\Controller\MemberCreditLogController@index',
                                    ] : null,
                            ]
                        ],
                        ModuleManager::getModuleConfigBoolean('Member', 'vipEnable') ?
                            [
                                'title' => '用户VIP订单',
                                'url' => '\Module\Member\Admin\Controller\MemberVipOrderController@index',
                            ] : null,
                        [
                            'title' => '用户设置',
                            'sort' => 999999,
                            'children' => [
                                [
                                    'title' => '用户设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@setting',
                                ],
                                [
                                    'title' => '用户协议',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@agreement',
                                ],
                                ModuleManager::getModuleConfigBoolean('Member', 'moneyEnable') ? [
                                    'title' => '钱包设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@money',
                                ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'vipEnable') ? [
                                    'title' => '用户VIP设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@vip',
                                ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'vipEnable') ? [
                                    'title' => '用户VIP等级',
                                    'url' => '\Module\Member\Admin\Controller\MemberVipSetController@index',
                                ] : null,
                                ModuleManager::getModuleConfigBoolean('Member', 'groupEnable') ? [
                                    'title' => '用户分组',
                                    'url' => '\Module\Member\Admin\Controller\MemberGroupController@index',
                                ] : null,
                            ]
                        ]
                    ]
                ],
            ];
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
