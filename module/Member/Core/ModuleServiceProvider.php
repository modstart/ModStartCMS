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
use Module\Member\Config\MemberMenu;
use Module\Member\Listener\MemberVipPayListener;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;
use Module\Vendor\Admin\Config\AdminWidgetLink;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        ModuleClassLoader::addNamespace('Overtrue\\Socialite', __DIR__ . '/../SDK/socialite/src');

        MemberMenu::register(function () {
            return [
                [
                    'icon' => 'user',
                    'title' => '我的',
                    'sort' => 1000,
                    'children' => [
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
                                [
                                    'title' => '微信授权登录',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@oauthWechatMobile',
                                ],
                                [
                                    'title' => '微信扫码登录',
                                    'url' => '\Module\Member\Admin\Controller\ConfigController@oauthWechat',
                                ],
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
