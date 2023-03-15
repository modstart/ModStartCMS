<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Module\ModuleManager;
use ModStart\Support\Concern\HasFields;
use Module\Member\Biz\Vip\MemberVipBiz;
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
                $builder->id('id', 'ID')->addable(true)->editable(true)
                    ->ruleUnique('member_vip_set')->required()
                    ->defaultValue(ModelUtil::sortNext('member_vip_set', [], 'id'));
                $builder->text('title', '名称')->required()->ruleUnique('member_vip_set');
                $builder->text('flag', '英文标识')->required()->ruleUnique('member_vip_set');
                $builder->switch('visible', '可见')->gridEditable(true)->tip('开启后前台用户可见');
                $builder->switch('isDefault', '默认')->optionsYesNo()->help('会员是否默认为该等级')->required();
                $builder->image('icon', '图标');
                $builder->currency('price', '价格')->required();
                $builder->number('vipDays', '时间')->required()->help('单位为天，365表示1年');
                $builder->text('desc', '简要说明')->required();
                $builder->richHtml('content', '详细说明')->required();
                if (ModuleManager::getModuleConfigBoolean('Member', 'creditEnable', false)) {
                    $builder->switch('creditPresentEnable', '赠送积分')->when('=', true, function ($form) {
                        /** @var Form $form */
                        $form->number('creditPresentValue', '赠送积分数量');
                    })->optionsYesNo()->listable(false);
                }
                foreach (MemberVipBiz::all() as $biz) {
                    $builder->layoutPanel($biz->title(), function ($builder) use ($biz) {
                        $biz->vipField($builder);
                    });
                }
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->like('title', '名称');
            })
            ->operateFixed('right')
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->canShow(false)
            ->title('VIP等级')
            ->addDialogSize(['600px', '95%'])
            ->editDialogSize(['600px', '95%'])
            ->hookSaved(function (Form $form) {
                MemberVipUtil::clearCache();
            });
    }
}
