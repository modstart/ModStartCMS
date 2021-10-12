<?php

namespace Module\AdminManager\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        AdminMenu::register([
            [
                'title' => L('Admin Manage'),
                'icon' => 'user-o',
                'sort' => 500,
                'children' => [
                    [
                        'title' => L('Admin Role'),
                        'url' => '\ModStart\Admin\Controller\AdminRoleController@index',
                    ],
                    [
                        'title' => L('Admin Role Manage'),
                        'rule' => 'AdminRoleManage',
                        'hide' => true,
                    ],
                    [
                        'title' => L('Admin User'),
                        'url' => '\ModStart\Admin\Controller\AdminUserController@index',
                    ],
                    [
                        'title' => L('Admin User Manage'),
                        'rule' => 'AdminUserManage',
                        'hide' => true,
                    ],
                    [
                        'title' => L('Admin Log'),
                        'url' => '\ModStart\Admin\Controller\AdminLogController@index',
                    ],
                    [
                        'title' => L('Admin Log Manage'),
                        'rule' => 'AdminLogManage',
                        'hide' => true,
                    ],
                    [
                        'title' => L('Change password'),
                        'url' => '\ModStart\Admin\Controller\ProfileController@changePassword',
                        'hide' => true,
                    ],
                ]
            ],
            [
                'title' => L('System Manage'),
                'icon' => 'code-alt',
                'sort' => 700,
                'children' => [
                    [
                        'title' => L('System Manage'),
                        'rule' => 'SystemManage',
                        'url' => '\ModStart\Admin\Controller\SystemController@index',
                        'hide' => true,
                    ],
                    [
                        'title' => L('Data File Manager View'),
                        'rule' => 'DataFileManagerView',
                        'url' => '\ModStart\Admin\Controller\DataController@index',
                        'hide' => true,
                    ],
                    [
                        'title' => L('Data File Manager Upload'),
                        'rule' => 'DataFileManagerUpload',
                        'hide' => true,
                    ],
                    [
                        'title' => L('Data File Manager Delete'),
                        'rule' => 'DataFileManagerDelete',
                        'hide' => true,
                    ],
                    [
                        'title' => L('Data File Manager Add/Edit'),
                        'rule' => 'DataFileManagerAdd/Edit',
                        'hide' => true,
                    ],
                    [
                        'title' => '系统升级',
                        'rule' => 'SystemUpgrade',
                        'hide' => true,
                    ]
                ]
            ]
        ]);
    }

    
    public function register()
    {

    }
}
