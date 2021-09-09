<?php

namespace Module\Site\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        AdminMenu::register([
            [
                'title' => L('Site Manage'),
                'icon' => 'cog',
                'sort' => 400,
                'children' => [
                    [
                        'title' => '基础配置',
                        'children' => [
                            [
                                'title' => '网站基础配置',
                                'url' => '\Module\Site\Admin\Controller\ConfigController@setting',
                            ],
                        ]
                    ],
                ]
            ]
        ]);
    }

    
    public function register()
    {

    }
}
