<?php

namespace Module\CmsThemeCorp\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Module\ModuleManager;
use Module\Banner\Provider\BannerPositionProvider;
use Module\Cms\Provider\Theme\CmsThemeProvider;
use Module\CmsThemeCorp\Provider\CmsThemeCorpBannerPositionProvider;
use Module\CmsThemeCorp\Provider\CorpSiteTemplateProvider;
use Module\CmsThemeCorp\Provider\CorpThemeProvider;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        SiteTemplateProvider::register(CorpSiteTemplateProvider::class);
        CmsThemeProvider::register(CorpThemeProvider::class);
        AdminMenu::register([
            [
                'title' => '功能设置',
                'icon' => 'tools',
                'sort' => 300,
                'children' => [
                    [
                        'title' => '模板设置',
                        'children' => [
                            [
                                'title' => 'CMS商务模板',
                                'url' => '\Module\CmsThemeCorp\Admin\Controller\ConfigController@index',
                            ],
                        ],
                    ],
                ]
            ],
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
