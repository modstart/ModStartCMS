<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function basic(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('企业信息设置');
        $builder->text('Cms_HomeInfoTitle', '首页介绍标题');
        $builder->image('Cms_HomeInfoImage', '首页介绍图片');
        $builder->richHtml('Cms_HomeInfoContent', '首页介绍说明');
        $builder->formClass('wide');
        return $builder->perform();
    }
}
