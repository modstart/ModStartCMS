<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Type\TypeUtil;
use Module\Cms\Type\PostEditorType;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('CMS设置');
        $builder->radio('Cms_PostDefaultEditorType', '默认文章发布编辑器')
            ->optionType(PostEditorType::class)
            ->help('默认为' . TypeUtil::name(PostEditorType::class, PostEditorType::RICH_TEXT));
        $builder->formClass('wide');
        return $builder->perform();
    }
}
