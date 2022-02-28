<?php

namespace Module\Survey\Core;

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
                    'title' => '问卷调查',
                    'icon' => 'list',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => '问卷活动',
                            'url' => '\Module\Survey\Admin\Controller\ActivityController@index',
                        ],
                        [
                            'title' => '问卷提交',
                            'url' => '\Module\Survey\Admin\Controller\AnswerController@index',
                        ],
                    ]
                ],
//                 [
//                     'title' => '功能设置',
//                     'icon' => 'tools',
//                     'sort' => 300,
//                     'children' => [
//                         [
//                             'title' => '问卷调查设置',
//                             'url' => '\Module\Survey\Admin\Controller\ConfigController@index',
//                         ],
//                     ]
//                 ],
//                 [
//                    'title' => L('Site Manage'),
//                    'icon' => 'cog',
//                    'sort' => 400,
//                    'children' => [
//                        [
//                            'title' => '问卷调查设置',
//                            'children' => [
//                                [
//                                    'title' => '问卷调查设置',
//                                    'url' => '\Module\Survey\Admin\Controller\ConfigController@index',
//                                ],
//                            ]
//                        ]
//                    ]
//                ]
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
