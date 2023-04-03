<?php

namespace Module\Cms\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Layout\Row;
use ModStart\Module\ModuleClassLoader;
use ModStart\Module\ModuleManager;
use Module\Cms\Provider\CmsHomePageProvider;
use Module\Cms\Provider\Theme\CmsThemeProvider;
use Module\Cms\Provider\Theme\DefaultThemeProvider;
use Module\Cms\Util\CmsModelUtil;
use Module\TagManager\Biz\TagManagerBiz;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;
use Module\Vendor\Admin\Config\AdminWidgetLink;
use Module\Vendor\Provider\HomePage\HomePageProvider;
use Module\Vendor\Provider\SearchBox\QuickSearchBoxProvider;
use Module\Vendor\Provider\SearchBox\SearchBoxProvider;
use Module\Vendor\Provider\SiteUrl\SiteUrlBiz;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        if (method_exists(ModuleClassLoader::class, 'addClass')) {
            ModuleClassLoader::addClass('MCms', __DIR__ . '/../Helpers/MCms.php');
        }
        SearchBoxProvider::register(
            QuickSearchBoxProvider::make('cms', '内容', modstart_web_url('search'), 100)
        );
        CmsThemeProvider::register(DefaultThemeProvider::class);
        HomePageProvider::register(CmsHomePageProvider::class);
        if (class_exists(SiteUrlBiz::class)) {
            SiteUrlBiz::register(CmsSiteUrlBiz::class);
        }

        AdminWidgetLink::register(function () {
            $menu = [];
            $menu[] = ['首页', modstart_web_url('cms')];
            $tree = TreeUtil::modelToTree('cms_cat', ['title' => 'title', 'url' => 'url'], 'id', 'pid', 'sort', [
                'enable' => true,
            ]);
            $categories = TreeUtil::treeToListWithIndent($tree, 'id', 'title', 0, ['url']);
            $menu = array_merge($menu, array_map(function ($record) {
                return [
                    '栏目:' . $record['title'],
                    modstart_web_url($record['url'] ? $record['url'] : 'c/' . $record['id']),
                ];
            }, $categories));
            return [
                AdminWidgetLink::build('CMS', $menu)
            ];
        });

        AdminMenu::register(function () {
            $models = CmsModelUtil::all();
            $contentMenus = [];
            foreach ($models as $model) {
                $contentMenus[] = [
                    'title' => $model['title'],
                    'rule' => 'CmsContentManage' . $model['id'],
                    'url' => action('\Module\Cms\Admin\Controller\ContentController@index', ['modelId' => $model['id']]),
                ];
            }
            return [
                [
                    'title' => 'CMS管理',
                    'icon' => 'credit',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => '栏目管理',
                            'url' => '\Module\Cms\Admin\Controller\CatController@index',
                        ],
                        [
                            'title' => '基础设置',
                            'url' => '\Module\Cms\Admin\Controller\ConfigController@basic',
                        ],
                        [
                            'title' => '内容管理',
                            'children' => $contentMenus
                        ],
                        [
                            'title' => 'CMS管理',
                            'children' => [
                                [
                                    'title' => '内容模型',
                                    'url' => '\Module\Cms\Admin\Controller\ModelController@index',
                                ],
                                [
                                    'title' => '模板管理',
                                    'url' => '\Module\Cms\Admin\Controller\TemplateController@index',
                                ],
                                [
                                    'title' => '数据备份',
                                    'url' => '\Module\Cms\Admin\Controller\BackupController@index',
                                ],
                                [
                                    'title' => '数据恢复',
                                    'url' => '\Module\Cms\Admin\Controller\RestoreController@index',
                                ],
                                [
                                    'title' => 'CMS设置',
                                    'url' => '\Module\Cms\Admin\Controller\ConfigController@setting',
                                ],
                            ]
                        ],
                    ]
                ],
            ];
        });

        AdminWidgetDashboard::registerIcon(function (Row $row) {
            $models = CmsModelUtil::all();
            foreach ($models as $model) {
                $row->column(3, DashboardItemA::makeIconNumberTitle(
                    'iconfont icon-details', ModelUtil::count('cms_content', ['modelId' => $model['id']]), $model['title'],
                    modstart_admin_url('cms/content/' . $model['id'])
                ));
            }
            $row->column(3, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-list-alt', ModelUtil::count('cms_cat'), '栏目数',
                modstart_admin_url('cms/cat')
            ));
            $row->column(3, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-credit',
                ModelUtil::count('cms_model'),
                '模型数',
                modstart_admin_url('cms/model')
            ));
        });

        if (modstart_module_enabled('TagManager')) {
            TagManagerBiz::register(CmsTagManagerBiz::class);
        }

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
