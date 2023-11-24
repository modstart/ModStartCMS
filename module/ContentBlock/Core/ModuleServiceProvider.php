<?php

namespace Module\ContentBlock\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleClassLoader;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        ModuleClassLoader::addClass('MContentBlock', __DIR__ . '/MContentBlock.php');

        AdminMenu::register(function () {
            return [
                [
                    'title' => '内容管理',
                    'icon' => 'file',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => '内容区块',
                            'url' => '\Module\ContentBlock\Admin\Controller\ContentBlockController@index',
                        ],
                    ]

                ],
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
