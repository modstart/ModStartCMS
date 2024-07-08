<?php

namespace Module\CmsMemberPost\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use Module\Member\Config\MemberHomeIcon;
use Module\Member\Config\MemberMenu;
use Module\Vendor\Admin\Widget\AdminWidgetLink;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        AdminMenu::register(function () {
            return [
                [
                    'title' => 'CMS管理',
                    'icon' => 'credit',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => 'CMS管理',
                            'children' => [
                                [
                                    'title' => '用户投稿设置',
                                    'url' => '\Module\CmsMemberPost\Admin\Controller\ConfigController@index',
                                ],
                            ]
                        ]
                    ]
                ],
            ];
        });
        if (modstart_config('CmsMemberPost_Enable', false)) {
            MemberMenu::register(function () {
                return [
                    [
                        'icon' => 'list-alt',
                        'title' => '内容',
                        'sort' => 900,
                        'children' => [
                            [
                                'title' => '发布内容',
                                'url' => modstart_web_url('cms_member_content/edit'),
                            ],
                            [
                                'title' => '我的内容',
                                'url' => modstart_web_url('cms_member_content'),
                            ],
                        ],
                    ],
                ];
            });
            MemberHomeIcon::register(function () {
                return [
                    [
                        'title' => '内容',
                        'sort' => 900,
                        'children' => [
                            [
                                'icon' => 'iconfont icon-edit',
                                'title' => '发布内容',
                                'url' => modstart_web_url('cms_member_content/edit'),
                            ],
                            [
                                'icon' => 'iconfont icon-list-alt',
                                'title' => '我的内容',
                                'url' => modstart_web_url('cms_member_content'),
                            ],
                        ]
                    ],
                ];
            });
        }
        AdminWidgetLink::register(function () {
            return AdminWidgetLink::build('会员', [
                ['发布内容', modstart_web_url('cms_member_content/edit')],
                ['我的内容', modstart_web_url('cms_member_content')],
            ]);
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
