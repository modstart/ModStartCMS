<?php

namespace Module\Site\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
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
                        'url' => '\Module\Site\Admin\Controller\ConfigController@setting',
                    ],
                ]
            ]
        ]);
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
