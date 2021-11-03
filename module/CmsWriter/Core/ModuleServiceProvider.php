<?php

namespace Module\CmsWriter\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Layout\Row;
use Module\CmsWriter\Provider\CmsWriterHomePageProvider;
use Module\Member\Config\MemberMenu;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;
use Module\Vendor\Admin\Config\AdminWidgetLink;
use Module\Vendor\Provider\HomePage\HomePageProvider;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        MemberMenu::register(function () {
            return [
                [
                    'icon' => 'list-alt',
                    'title' => '文章管理',
                    'sort' => 100,
                    'children' => [
                        [
                            'title' => '文章分类',
                            'url' => modstart_web_url('writer/category'),
                        ],
                        [
                            'title' => '文章管理',
                            'url' => modstart_web_url('writer/post'),
                        ],
                        [
                            'title' => '写作设置',
                            'url' => modstart_web_url('writer/setting'),
                        ],
                    ]
                ],
            ];
        });

        AdminWidgetLink::register(function () {
            $menu = [];
            $menu[] = ['首页', modstart_web_url('cms')];
            $tree = TreeUtil::modelToTree('cms_channel', ['title' => 'title', 'alias' => 'alias']);
            $categories = TreeUtil::treeToListWithIndent($tree, 'id', 'title', 0, ['alias']);
            $menu = array_merge($menu, array_map(function ($record) {
                return [
                    '频道:' . $record['title'],
                    modstart_web_url("channel/$record[alias]"),
                ];
            }, $categories));
            return [
                AdminWidgetLink::build('文章投稿', $menu)
            ];
        });

        AdminMenu::register(function () {
            return [
                [
                    'title' => '文章内容',
                    'icon' => 'category',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => '频道管理',
                            'url' => '\Module\CmsWriter\Admin\Controller\ChannelController@index',
                        ],
                        [
                            'title' => '系统文章',
                            'url' => '\Module\CmsWriter\Admin\Controller\PostSystemController@index',
                        ],
                        [
                            'title' => '用户文章',
                            'url' => '\Module\CmsWriter\Admin\Controller\PostController@index',
                        ],
                    ]
                ],
                [
                    'title' => '功能设置',
                    'icon' => 'tools',
                    'sort' => 300,
                    'children' => [
                        [
                            'title' => 'CMS设置',
                            'url' => '\Module\CmsWriter\Admin\Controller\ConfigController@setting',
                        ],
                    ]
                ]
            ];
        });

        AdminWidgetDashboard::registerIcon(function (Row $row) {
            $row->column(3, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-list-alt', ModelUtil::count('cms_channel'), '频道数',
                modstart_admin_url('cms/channel')
            ));
            $row->column(3, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-file',
                ModelUtil::model('cms_post')->where('memberUserId', '>', 0)->count(),
                '用户文章数',
                modstart_admin_url('cms/post')
            ));
            $row->column(3, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-file',
                ModelUtil::model('cms_post')->where('memberUserId', 0)->count(),
                '系统文章数',
                modstart_admin_url('cms/post_system')
            ));
        });

        HomePageProvider::register(CmsWriterHomePageProvider::class);

    }

    
    public function register()
    {

    }
}
