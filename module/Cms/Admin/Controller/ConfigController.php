<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function basic(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('CMS基础信息');

        $builder->text('Cms_CompanyName', '公司名称');
        $builder->text('Cms_ContactEmail', '公司邮箱');
        $builder->text('Cms_ContactPhone', '公司电话');
        $builder->text('Cms_ContactAddress', '公司地址');

        $builder->text('Cms_HomeInfoTitle', '首页介绍标题');
        $builder->image('Cms_HomeInfoImage', '首页介绍图片');
        $builder->richHtml('Cms_HomeInfoContent', '首页介绍说明');
        $builder->text('Cms_HomeInfoLinkText', '首页介绍链接文字');
        $builder->text('Cms_HomeInfoLink', '首页介绍链接');

        $builder->text('Cms_FooterNavTitle', '底部导航标题')->help('默认为 关于');
        $builder->text('Cms_FooterNavSecondaryTitle', '底部次导航标题')->help('默认为 导航');

        $builder->formClass('wide');
        return $builder->perform();
    }
}
