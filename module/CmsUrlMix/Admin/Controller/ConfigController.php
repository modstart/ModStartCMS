<?php

namespace Module\CmsUrlMix\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function index(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('链接增强设置');
        $builder->switch('CmsUrlMix_Enable', '开启CMS链接增强');
        $builder->textarea('CmsUrlMix_ContentUrlRoutes', '内容页全路径路由规则')
            ->placeholder('每行一个')
            ->help('为了提高匹配效率，默认路由不会匹配到内容页，需要配置全路径路由规则，如 product/view/{id}');
        $builder->formClass('wide');
        return $builder->perform();
    }

}
