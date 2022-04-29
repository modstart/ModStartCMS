<?php


namespace Module\TagManager\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\TagManager\Biz\TagManagerBiz;

class TagManagerController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('tag_manager')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->type('biz', '业务')->type(TagManagerBiz::allMap())->editable(false);
                $builder->text('tag', '标签')->editable(false);
                $builder->switch('isShow', '前台显示')->gridEditable(true)->width(100);
                $builder->number('cnt', '数量');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('biz', '业务')->select(TagManagerBiz::allMap());
                $filter->like('tag', '标签');
            })
            ->canShow(false)
            ->title('标签云');
    }
}
