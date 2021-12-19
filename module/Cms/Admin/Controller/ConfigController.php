<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function basic(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('基础信息');

        $builder->text('Cms_CompanyName', '企业名称');
        $builder->text('Cms_ContactEmail', '企业邮箱');
        $builder->text('Cms_ContactPhone', '企业电话');
        $builder->text('Cms_ContactAddress', '企业地址');

        $builder->text('Cms_HomeInfoTitle', '企业介绍标题');
        $builder->image('Cms_HomeInfoImage', '企业介绍图片');
        $builder->richHtml('Cms_HomeInfoContent', '企业介绍说明');

        $builder->formClass('wide');
        return $builder->perform();
    }
}
