<?php

namespace Module\Site\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Form\Form;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('基础配置');
        $builder->layoutPanel('网站信息', function (Form $form) {
            $form->image('siteLogo', '网站Logo');
            $form->text('siteName', '网站名称');
            $form->text('siteSlogan', '网站副标题');
            $form->text('siteDomain', '网站域名');
            $form->text('siteKeywords', '网站关键词');
            $form->textarea('siteDescription', '网站描述');
            $form->image('siteFavIco', '网站ICO');
        });
        $builder->layoutPanel('模板主题', function (Form $form) {
            $form->color('sitePrimaryColor', '网站主色调');
            $form->select('siteTemplate', '网站模板')->options(SiteTemplateProvider::map());
        });
        $builder->layoutPanel('备案信息', function (Form $form) {
            $form->text('siteBeian', 'ICP备案编号');
            $form->text('siteBeianGonganText', '公安备案文字');
            $form->text('siteBeianGonganLink', '公安备案链接');
        });
        $builder->formClass('wide');
        return $builder->perform();
    }

}
