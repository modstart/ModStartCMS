<?php

namespace Module\AdminManager\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
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
                    'title' => L('AdminManage'),
                    'icon' => 'user-o',
                    'sort' => 500,
                    'children' => [
                        [
                            'title' => L('AdminUser'),
                            'url' => '\ModStart\Admin\Controller\AdminUserController@index',
                        ],
                        [
                            'title' => L('AdminUserManage'),
                            'rule' => 'AdminUserManage',
                            'hide' => true,
                        ],
                        [
                            'title' => L('AdminRole'),
                            'url' => '\ModStart\Admin\Controller\AdminRoleController@index',
                        ],
                        [
                            'title' => L('AdminRoleManage'),
                            'rule' => 'AdminRoleManage',
                            'hide' => true,
                        ],
                        [
                            'title' => L('AdminLog'),
                            'url' => '\ModStart\Admin\Controller\AdminLogController@index',
                        ],
                        [
                            'title' => L('AdminLogManage'),
                            'rule' => 'AdminLogManage',
                            'hide' => true,
                        ],
                        [
                            'title' => L('ChangePassword'),
                            'url' => '\ModStart\Admin\Controller\ProfileController@changePassword',
                            'hide' => true,
                        ],
                    ]
                ],
                [
                    'title' => L('SystemManage'),
                    'icon' => 'code-alt',
                    'sort' => 700,
                    'children' => [
                        [
                            'title' => L('SystemManage'),
                            'rule' => 'SystemManage',
                            'url' => '\ModStart\Admin\Controller\SystemController@index',
                            'hide' => true,
                        ],
                        [
                            'title' => L('DataFileManagerView'),
                            'rule' => 'DataFileManagerView',
                            'url' => '\ModStart\Admin\Controller\DataController@index',
                            'hide' => true,
                        ],
                        [
                            'title' => L('DataFileManagerUpload'),
                            'rule' => 'DataFileManagerUpload',
                            'hide' => true,
                        ],
                        [
                            'title' => L('DataFileManagerDelete'),
                            'rule' => 'DataFileManagerDelete',
                            'hide' => true,
                        ],
                        [
                            'title' => L('DataFileManagerAddEdit'),
                            'rule' => 'DataFileManagerAdd/Edit',
                            'hide' => true,
                        ],
                        [
                            'title' => L('SystemUpgrade'),
                            'rule' => 'SystemUpgrade',
                            'hide' => true,
                        ]
                    ]
                ]
            ];
        });

        AdminWidgetLink::register(function () {
            $menu = [];
            $menu[] = [L('Home'), modstart_web_url('')];
            return AdminWidgetLink::build(L('System'), $menu);
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
