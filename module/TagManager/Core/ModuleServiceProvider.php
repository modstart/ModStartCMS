<?php

namespace Module\TagManager\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use Module\Vendor\Admin\Config\AdminWidgetLink;

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
                    'title' => '内容管理',
                    'icon' => 'file',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => '标签云',
                            'children' => [
                                [
                                    'title' => '标签云内容',
                                    'url' => '\Module\TagManager\Admin\Controller\TagManagerController@index',
                                ],
                                [
                                    'title' => '标签云重建',
                                    'url' => '\Module\TagManager\Admin\Controller\TagManagerBuildController@index',
                                ],
                            ],
                        ],
                    ]
                ],
            ];
        });
        AdminWidgetLink::register(function () {
            return AdminWidgetLink::build('其他', [
                ['标签云', modstart_web_url('tag_manager')],
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
