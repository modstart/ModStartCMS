<?php


namespace Module\CmsWriter\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Type\TypeUtil;
use Module\CmsWriter\Type\PostEditorType;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('文章投稿系统');
        $builder->radio('CmsWriter_PostDefaultEditorType', '默认文章发布编辑器')
            ->optionType(PostEditorType::class)
            ->help('默认为' . TypeUtil::name(PostEditorType::class, PostEditorType::RICH_TEXT));
        $builder->formClass('wide');
        return $builder->perform();
    }
}
