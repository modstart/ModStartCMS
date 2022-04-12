<?php

namespace Module\CmsUrlMix\Core;

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
                    'title' => 'CMS管理',
                    'icon' => 'credit',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => 'CMS管理',
                            'children' => [
                                [
                                    'title' => '链接增强设置',
                                    'url' => '\Module\CmsUrlMix\Admin\Controller\ConfigController@index',
                                ],
                            ]
                        ],
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
