<?php


namespace Module\Vendor\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Module\ModuleManager;
use Module\Vendor\Admin\Widget\AdminWidgetLink;

class WidgetLinkController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    private function build($groupName, $titleLinks)
    {
        if (empty($titleLinks)) {
            return null;
        }
        return [
            'title' => $groupName,
            'list' => array_filter(array_map(function ($item) {
                return $item ? [
                    'title' => $item[0],
                    'link' => $item[1],
                ] : null;
            }, $titleLinks))
        ];
    }

    public function select()
    {
        $links = [];
        $links[] = $this->build('系统', [
            ['首页', modstart_web_url('')],
        ]);
        $links = array_merge($links, AdminWidgetLink::get());
        /*
        if (ModuleManager::isModuleInstalled('LandingPage')) {
            $prefix = ModuleManager::getModuleConfig('LandingPage', 'prefix', modstart_web_url('p/'));
            $links[] = $this->build('落地页', array_map(function ($record) use ($prefix) {
                return [$record['title'], $prefix . $record['url']];
            }, ModelUtil::all('landing_page')));
        }
        if (ModuleManager::isModuleInstalled('Product')) {
            $links[] = $this->build('产品', array_merge(
                [['产品', '/product']],
                array_map(function ($record) {
                    return ['|-' . $record['title'], modstart_web_url("product?categoryId=$record[id]")];
                }, ModelUtil::all('product_category'))
            ));
        }
        if (ModuleManager::isModuleInstalled('Post')) {
            $tree = TreeUtil::modelToTree('post_category', ['title' => 'title']);
            $records = TreeUtil::treeToListWithLevel($tree);
            $links[] = $this->build('分类文章', array_merge(
                array_map(function ($record) {
                    return [str_repeat('|-', $record['level']) . $record['title'], modstart_web_url("post?categoryId=$record[id]")];
                }, $records)
            ));
        }
        if (ModuleManager::isModuleInstalled('News')) {
            $links[] = $this->build('新闻资讯', array_merge(
                [['新闻资讯', modstart_web_url('news')]],
                array_map(function ($record) {
                    return ['|-' . $record['title'], modstart_web_url("news?categoryId=$record[id]")];
                }, ModelUtil::all('news_category'))
            ));
        }
        if (ModuleManager::isModuleInstalled('Cases')) {
            $links[] = $this->build('案例', array_merge(
                [['案例', modstart_web_url('case')]],
                array_map(function ($record) {
                    return ['|-' . $record['title'], modstart_web_url("case?categoryId=$record[id]")];
                }, ModelUtil::all('news_category'))
            ));
        }
        if (ModuleManager::isModuleInstalled('CmsJob')) {
            $links[] = $this->build('招聘', array_merge(
                [['招聘', modstart_web_url('job')]],
                array_map(function ($record) {
                    return ['|-' . $record['title'], modstart_web_url("job/$record[id]")];
                }, ModelUtil::all('cms_job'))
            ));
        }
        if (ModuleManager::isModuleInstalled('CmsBook')) {
            $links[] = $this->build('预约', [
                ['接口-预约提交', modstart_web_url('api/book/submit_direct')]
            ]);
        }
        */
        return view('modstart::admin.dialog.linkSelector', [
            'links' => array_filter($links),
        ]);
    }
}
