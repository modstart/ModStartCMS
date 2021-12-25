<?php


namespace Module\CmsThemeCorp\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\Cms\Traits\CmsSiteTemplateFillDataTrait;

class ConfigController extends Controller
{
    use CmsSiteTemplateFillDataTrait;

    public function index(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('CMS商务模板');
        $url = action('\\' . __CLASS__ . '@fillData');
        $builder->text('CmsThemeCorp_HomeInfoLinkText', '企业介绍链接文字');
        $builder->text('CmsThemeCorp_HomeInfoLink', '企业介绍链接');
        $builder->display('_display', '')->content('<a href="javascript:;" data-dialog-request="' . $url . '"><i class="iconfont icon-credit"></i> 初始化演示数据</a>')->addable(true);
        $builder->formClass('wide');
        return $builder->perform();
    }


}
