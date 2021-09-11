<?php

namespace Module\SiteCounter\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Hook\ModStartHook;

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
                                'title' => '网站统计设置',
                                'url' => '\Module\SiteCounter\Admin\Controller\ConfigController@setting',
                            ],
                        ]
                    ]
                ]
            ]
        ]);
        if (modstart_config('systemCounter')) {
            ModStartHook::subscribe('PageHeadAppend', function () {
                return modstart_config('systemCounter');
            });
        }
        if (modstart_config('systemCounterBody')) {
            ModStartHook::subscribe('PageBodyAppend', function () {
                return modstart_config('systemCounterBody');
            });
        }
    }

    
    public function register()
    {

    }
}
