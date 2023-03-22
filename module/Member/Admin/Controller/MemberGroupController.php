<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Util\MemberGroupUtil;

class MemberGroupController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_group')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID')->editable(true)->addable(true);
                $builder->text('title', '名称');
                $builder->text('description', '描述');
                $builder->switch('isDefault', '默认')->optionsYesNo()->help('');
                $builder->switch('showFront', '前台显示')->optionsYesNo()->help('');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->like('title', '名称');
            })
            ->enablePagination(false)
            ->defaultOrder(['id', 'asc'])
            ->canSort(true)
            ->title('用户分组')
            ->dialogSizeSmall()
            ->hookSaved(function (Form $form) {
                MemberGroupUtil::clearCache();
            });
        $builder->repository()->setSortColumn('id');
    }
}
