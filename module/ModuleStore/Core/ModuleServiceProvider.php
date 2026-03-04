<?php

namespace Module\ModuleStore\Core;

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
        AdminMenu::register(function () {
            return [
                [
                    'title' => L('SystemManage'),
                    'icon' => 'code-alt',
                    'sort' => 700,
                    'children' => [
                        [
                            'title' => L('ModuleManage'),
                            'rule' => 'ModuleStoreManage',
                            'url' => '\Module\ModuleStore\Admin\Controller\ModuleStoreController@index',
                        ]
                    ]
                ]
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
