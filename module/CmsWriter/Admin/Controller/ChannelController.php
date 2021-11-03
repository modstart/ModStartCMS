<?php


namespace Module\CmsWriter\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\CmsWriter\Util\ChannelUtil;

class ChannelController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('cms_channel')
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->text('title', '名称')->required();
                $builder->text('alias', '别名')->required()
                    ->help('字母数字下划线，可以通过URL访问 channel/{alias}')
                    ->ruleUnique('cms_channel')
                    ->ruleRegex('/^[a-zA-Z0-9_]+$/');
                $builder->image('cover', '封面');
                $builder->textarea('description', '描述');
                $builder->switch('pushEnable', '允许投稿')->width(100);
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', L('Title'));
            })
            ->hookChanged(function (Form $form) {
                ChannelUtil::clearCache();
            })
            ->dialogSizeSmall()
            ->title('频道管理')
            ->asTree()
            ->treeMaxLevel(2);
    }
}
