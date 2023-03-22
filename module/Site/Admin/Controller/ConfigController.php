<?php

namespace Module\Site\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('基本设置');

        $builder->layoutSeparator('网站信息');
        $builder->image('siteLogo', '网站Logo');
        $builder->text('siteName', '网站名称');
        $builder->text('siteSlogan', '网站副标题');
        $builder->text('siteDomain', '网站域名')->help('如 xxx.com');
        $builder->text('siteUrl', '网站地址')->help('如 https://xxx.com 主要用于后台任务地址转换');
        $builder->text('siteKeywords', '网站关键词');
        $builder->textarea('siteDescription', '网站描述');
        $builder->image('siteFavIco', '网站ICO');

        $builder->layoutSeparator('模板主题');
        $builder->color('sitePrimaryColor', '网站主色调');
        $builder->select('siteTemplate', '网站模板')->options(SiteTemplateProvider::map());

        $builder->layoutSeparator('备案信息');
        $builder->text('siteBeian', 'ICP备案编号');
        $builder->text('siteBeianGonganText', '公安备案文字');
        $builder->text('siteBeianGonganLink', '公安备案链接');
        $builder->textarea('Site_CopyrightOthers', '其他备案信息')->help('支持HTML');

        $builder->layoutSeparator('联系信息');
        $builder->text('Site_ContactEmail', '邮箱');
        $builder->text('Site_ContactPhone', '电话');
        $builder->text('Site_ContactAddress', '地址');
        $builder->image('Site_ContactQrcode', '联系二维码')->help('可传带二维码的公众号/微信/QQ等，方便用户扫码联系');

        $builder->formClass('wide');
        return $builder->perform();
    }

}
