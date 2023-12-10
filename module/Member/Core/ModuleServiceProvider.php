<?php

namespace Module\Member\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ColorUtil;
use ModStart\Data\Event\DataDeletedEvent;
use ModStart\Data\Event\DataUploadedEvent;
use ModStart\Data\Event\DataUploadingEvent;
use ModStart\Layout\Row;
use ModStart\Module\ModuleManager;
use Module\Member\Auth\MemberUser;
use Module\Member\Config\MemberHomeIcon;
use Module\Member\Config\MemberMenu;
use Module\Member\Events\MemberUserRegisteredEvent;
use Module\Member\Model\MemberUpload;
use Module\Member\Provider\MemberAdminShowPanel\MemberAdminShowPanelProvider;
use Module\Member\Provider\MemberDeleteScheduleProvider;
use Module\Member\Provider\VerifySmsTemplateProvider;
use Module\Member\Util\MemberCreditUtil;
use Module\Member\Util\MemberDataStatisticUtil;
use Module\Member\Util\MemberMessageUtil;
use Module\Member\Util\MemberMoneyUtil;
use Module\Member\Util\MemberParamUtil;
use Module\Member\Util\MemberUtil;
use Module\PayCenter\Biz\PayCenterBiz;
use Module\Vendor\Admin\Widget\AdminWidgetDashboard;
use Module\Vendor\Admin\Widget\AdminWidgetLink;
use Module\Vendor\Job\MailSendJob;
use Module\Vendor\Provider\Schedule\ScheduleBiz;
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
            $moneyEnable = ModuleManager::getModuleConfig('Member', 'moneyEnable', false);
            $creditEnable = ModuleManager::getModuleConfig('Member', 'creditEnable', false);
            $creditName = '我的' . ModuleManager::getModuleConfig('Member', 'creditName', '积分');
            $addressEnable = ModuleManager::getModuleConfig('Member', 'addressEnable', false);
            return [
                [
                    'icon' => 'details',
                    'title' => '资产',
                    'sort' => 900,
                    'children' => [
                        $moneyEnable ? [
                            'title' => '我的钱包',
                            'url' => modstart_web_url('member_money'),
                        ] : null,
                        $creditEnable ? [
                            'title' => $creditName,
                            'url' => modstart_web_url('member_credit'),
                        ] : null,
                    ],
                ],
                [
                    'icon' => 'user',
                    'title' => '我的',
                    'sort' => 1000,
                    'children' => [
                        $addressEnable ? [
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
        ScheduleBiz::register(MemberDeleteScheduleProvider::class);

        MemberHomeIcon::register(function () {
            $moneyEnable = ModuleManager::getModuleConfig('Member', 'moneyEnable', false);
            $creditEnable = ModuleManager::getModuleConfig('Member', 'creditEnable', false);
            $creditName = '我的' . ModuleManager::getModuleConfig('Member', 'creditName', '积分');
            return [
                [
                    'title' => '我的',
                    'sort' => 1000,
                    'children' => [
                        $moneyEnable ? [
                            'icon' => 'iconfont icon-pay',
                            'value' => sprintf('￥%.2f', MemberMoneyUtil::getTotal(MemberUser::id())),
                            'title' => '我的钱包',
                            'url' => modstart_web_url('member_money'),
                        ] : null,
                        $creditEnable ? [
                            'icon' => 'iconfont icon-credit',
                            'value' => MemberCreditUtil::getTotal(MemberUser::id()),
                            'title' => $creditName,
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
                '用户管理',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
        });

        AdminWidgetLink::register(function () {
            return AdminWidgetLink::build('会员', [
                ['注册', modstart_web_url('register')],
                ['登录', modstart_web_url('login')],
                ['找回密码', modstart_web_url('retrieve')],
                ModuleManager::getModuleConfig('Member', 'vipEnable', false) ? ['开通VIP', modstart_web_url('member_vip')] : null,
                ModuleManager::getModuleConfig('Member', 'moneyEnable', false) ? ['用户钱包', modstart_web_url('member_money')] : null,
                ModuleManager::getModuleConfig('Member', 'creditEnable', false) ? ['用户积分', modstart_web_url('login')] : null,
            ]);
        });

        if (modstart_module_enabled('PayCenter')) {
            PayCenterBiz::register(MemberMoneyChargePayCenterBiz::class);
            PayCenterBiz::register(MemberVipPayCenterBiz::class);
        }

        if (ModuleManager::getModuleConfig('Member', 'dataStatisticEnable', false)) {
            MemberAdminShowPanelProvider::register(MemberDataStatisticAdminShowPanelProvider::class);
            if (class_exists(DataUploadingEvent::class)) {
                DataUploadingEvent::listen('member_upload', function (DataUploadingEvent $e) {
                    MemberDataStatisticUtil::checkQuota($e->userId);
                });
            }
            if (class_exists(DataUploadedEvent::class)) {
                DataUploadedEvent::listen('member_upload', function (DataUploadedEvent $e) {
                    MemberDataStatisticUtil::updateMemberUserUsedSize($e->userId);
                });
            }
            if (class_exists(DataDeletedEvent::class)) {
                DataDeletedEvent::listen(function (DataDeletedEvent $e) {
                    $record = MemberUpload::where(['dataId' => $e->data['id']])->first();
                    if ($record) {
                        $record->delete();
                        MemberDataStatisticUtil::updateMemberUserUsedSize($record->userId);
                    }
                });
            }
        }

        Event::listen(MemberUserRegisteredEvent::class, function (MemberUserRegisteredEvent $e) {
            $message = modstart_config('Member_Registered_Message', '');
            if ($message) {
                $memberUser = MemberUtil::getCached($e->memberUserId);
                $message = MemberParamUtil::replaceParam($message, $memberUser);
                MemberMessageUtil::send($e->memberUserId, MemberParamUtil::replaceParam($message, $memberUser));
            }
            $emailContent = modstart_config('Member_Registered_Email', '');
            $emailTitle = modstart_config('Member_Registered_EmailTitle', '');
            if ($emailTitle && $emailContent) {
                $memberUser = MemberUtil::getCached($e->memberUserId);
                if (!empty($memberUser['email'])) {
                    $emailTitle = MemberParamUtil::replaceParam($emailTitle, $memberUser);
                    $emailContent = MemberParamUtil::replaceParam($emailContent, $memberUser);
                    MailSendJob::createHtml($memberUser['email'], $emailTitle, $emailContent);
                }
            }
        });

        AdminMenu::register(function () {
            $moneyEnable = ModuleManager::getModuleConfig('Member', 'moneyEnable', false);
            $creditEnable = ModuleManager::getModuleConfig('Member', 'creditEnable', false);
            $vipEnable = ModuleManager::getModuleConfig('Member', 'vipEnable', false);
            $groupEnable = ModuleManager::getModuleConfig('Member', 'groupEnable', false);
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
                                $moneyEnable ?
                                    [
                                        'title' => '用户钱包流水',
                                        'url' => '\Module\Member\Admin\Controller\MemberMoneyLogController@index',
                                    ] : null,
                                $moneyEnable ?
                                    [
                                        'title' => '用户钱包提现申请',
                                        'url' => '\Module\Member\Admin\Controller\MemberMoneyCashController@index',
                                    ] : null,
                                $creditEnable ?
                                    [
                                        'title' => '用户积分流水',
                                        'url' => '\Module\Member\Admin\Controller\MemberCreditLogController@index',
                                    ] : null,
                            ]
                        ],
                        [
                            'title' => '用户设置',
                            'sort' => 999999,
                            'children' => [
                                [
                                    'title' => '注册登录',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@setting',
                                ],
                                [
                                    'title' => '用户协议',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@agreement',
                                ],
                                [
                                    'title' => '消息设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@message',
                                ],
                                $moneyEnable ? [
                                    'title' => '钱包设置',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@money',
                                ] : null,
                                $vipEnable ? [
                                    'title' => '用户VIP等级',
                                    'url' => '\Module\Member\Admin\Controller\MemberVipSetController@index',
                                ] : null,
                                $groupEnable ? [
                                    'title' => '用户分组',
                                    'url' => '\Module\Member\Admin\Controller\MemberGroupController@index',
                                ] : null,
                            ]
                        ]
                    ]
                ],
                [
                    'title' => '财务中心',
                    'icon' => 'cny',
                    'sort' => 200,
                    'children' => [
                        [
                            'title' => '业务订单',
                            'sort' => 999999,
                            'children' => [
                                $moneyEnable ?
                                    [
                                        'title' => '用户-钱包充值订单',
                                        'url' => '\Module\Member\Admin\Controller\MemberMoneyChargeOrderController@index',
                                    ] : null,
                                $vipEnable ?
                                    [
                                        'title' => '用户-VIP订单',
                                        'url' => '\Module\Member\Admin\Controller\MemberVipOrderController@index',
                                    ] : null,
                            ],
                        ],
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
