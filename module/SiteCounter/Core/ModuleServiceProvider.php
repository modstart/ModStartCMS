<?php

namespace Module\SiteCounter\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Hook\ModStartHook;

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
                    'title' => L('SiteManage'),
                    'icon' => 'cog',
                    'sort' => 400,
                    'children' => [
                        [
                            'title' => '统计代码设置',
                            'url' => '\Module\SiteCounter\Admin\Controller\ConfigController@setting',
                        ],
                    ]
                ]
            ];
        });
        ModStartHook::subscribe('PageHeadAppend', function () {
            return modstart_config('systemCounter');
        });
        ModStartHook::subscribe('DialogPageHeadAppend', function () {
            return modstart_config('systemCounter');
        });
        ModStartHook::subscribe('PageBodyAppend', function () {
            return modstart_config('systemCounterBody');
        });
        ModStartHook::subscribe('DialogPageBodyAppend', function () {
            return modstart_config('systemCounterBody');
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
