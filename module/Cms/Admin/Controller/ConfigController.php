<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Form\Form;
use Module\Cms\Type\ContentUrlMode;

class ConfigController extends Controller
{
    public static function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('CMS设置');

        $builder->layoutPanel('文案设置', function (Form $builder) {
            $builder->text('Cms_CompanyName', '企业名称');
            // $builder->text('Cms_ContactEmail', '企业邮箱');
            // $builder->text('Cms_ContactPhone', '企业电话');
            // $builder->text('Cms_ContactAddress', '企业地址');
            $builder->text('Cms_ContactFax', '企业传真');
            $builder->text('Cms_ContactContactPerson', '联系人');
            $builder->text('Cms_ContactQQ', '企业联系QQ');

            $builder->text('Cms_HomeInfoTitle', '企业介绍标题');
            $builder->image('Cms_HomeInfoImage', '企业介绍图片');
            $builder->richHtml('Cms_HomeInfoContent', '企业介绍说明');
        });

        $builder->layoutPanel('使用设置', function (Form $builder) {
            $builder->radio('Cms_ContentUrlMode', '内容URL模式')->optionType(ContentUrlMode::class)->defaultValue(ContentUrlMode::A);
            $builder->radio('Cms_CatAdminTreeMode', '后台栏目管理模式')->options([
                'tree' => '全部显示',
                'list' => '只显示一级',
            ])->defaultValue('list');
            $builder->switch('Cms_LikeAnonymityEnable', '匿名点赞')->defaultValue(false);
        });

        $builder->formClass('wide');
        return $builder->perform();
    }
}
