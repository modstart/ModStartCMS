<?php

namespace Module\Article\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Dao\ModelUtil;
use Module\Vendor\Admin\Widget\AdminWidgetLink;

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
                    'title' => '物料管理',
                    'icon' => 'description',
                    'sort' => 200,
                    'children' => [
                        [
                            'title' => '通用文章',
                            'url' => '\Module\Article\Admin\Controller\ArticleController@index',
                        ],
                    ]
                ]
            ];
        });
        AdminWidgetLink::register(function () {
            return AdminWidgetLink::build('内容', array_map(function ($record) {
                return [
                    $record['title'],
                    modstart_web_url($record['alias'] ? "article/$record[alias]" : "article/$record[id]"),
                ];
            }, ModelUtil::all('article')));
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
