<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Util\MemberVipUtil;

class MemberVipSetController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_vip_set')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID')->addable(true)->editable(true);
                $builder->text('title', '名称');
                $builder->text('flag', '英文标识');
                $builder->switch('isDefault', '默认')->optionsYesNo()->help('会员是否默认为该等级');
                $builder->currency('price', '价格');
                $builder->number('vipDays', '天数');
                $builder->richHtml('content', '说明');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->like('title', '名称');
            })
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->title('VIP等级')
            ->addDialogSize(['600px', '95%'])
            ->editDialogSize(['600px', '95%'])
            ->hookSaved(function (Form $form) {
                MemberVipUtil::clearCache();
            });
    }
}
