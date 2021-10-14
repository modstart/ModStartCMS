<?php

namespace Module\Site\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('基础设置');
        $builder->image('siteLogo', '网站Logo');
        $builder->text('siteName', '网站名称');
        $builder->text('siteSlogan', '网站副标题');
        $builder->text('siteDomain', '网站域名');
        $builder->text('siteKeywords', '网站关键词');
        $builder->textarea('siteDescription', '网站描述');
        $builder->text('siteBeian', '备案编号');
        $builder->image('siteFavIco', '网站ICO');
        $builder->color('sitePrimaryColor', '网站主色调');
        $builder->select('siteTemplate', '网站模板')->options(SiteTemplateProvider::map());
        return $builder->perform();
    }

}
