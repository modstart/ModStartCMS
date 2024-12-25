<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\RandomUtil;
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
                $builder->layoutPanel('基础信息', function ($builder) {
                    $builder->id('id', 'ID')->addable(true)->editable(true)
                        ->ruleUnique('member_vip_set')->required()
                        ->defaultValue(ModelUtil::sortNext('member_vip_set', [], 'id'));
                    $builder->text('title', '名称')->required()->ruleUnique('member_vip_set');
                    $builder->text('flag', '英文标识')->required()->ruleUnique('member_vip_set');
                    $builder->switch('visible', '可见')->gridEditable(true)->tip('开启后前台用户VIP开通页面可见');
                    $builder->switch('isDefault', '默认')->optionsYesNo()->help('会员是否默认为该等级')->required();
                    $builder->image('icon', '图标');
                    $builder->currency('price', '价格')->required();
                    $builder->currency('priceMarket', '划线价格')->required();
                    $builder->number('vipDays', '时间')->required()->help('单位为天，365表示1年');
                    $builder->text('desc', '简要说明')->required();
                    $builder->richHtml('content', '详细说明')->required();
                    if (ModuleManager::getModuleConfig('Member', 'creditEnable', false)) {
                        $builder->switch('creditPresentEnable', '赠送积分')->when('=', true, function ($form) {
                            /** @var Form $form */
                            $form->number('creditPresentValue', '赠送积分数量');
                        })->optionsYesNo()->listable(false);
                    }
                });
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
            ->gridOperateAppend(
                '
<a href="javascript:;" class="btn btn-primary" data-dialog-width="90%" data-dialog-height="90%" data-dialog-request="' . modstart_admin_url('member_vip_set/config') . '">
    <i class="iconfont icon-cog"></i>
    功能设置
</a>
<a href="javascript:;" class="btn btn-primary" data-dialog-width="90%" data-dialog-height="90%" data-dialog-request="' . modstart_admin_url('member_vip_right') . '">
    <i class="iconfont icon-cube"></i>
    权益设置
</a>
'
            )
            ->operateFixed('right')
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->canShow(false)
            ->title('用户VIP')
            ->addDialogSize(['600px', '95%'])
            ->editDialogSize(['600px', '95%'])
            ->hookSaved(function (Form $form) {
                MemberVipUtil::clearCache();
            });
    }

    public function config(AdminConfigBuilder $builder)
    {
        $builder->useDialog();
        $builder->pageTitle('功能设置');
        $builder->number('Member_VipCountDown', '倒计时长度')->unit('秒')->defaultValue(1800);
        $openUserDefaults = [];
        for ($i = 0; $i < 10; $i++) {
            $openUserDefaults[] = [
                'name' => RandomUtil::string(10),
                'time' => '刚刚',
                'title' => 'VIP黄金会员'
            ];
        }
        $builder->complexFieldsList('Member_VipOpenUsers', '虚拟开通用户')
            ->fields([
                ['name' => 'name', 'title' => '用户', 'type' => 'text', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                ['name' => 'time', 'title' => '时间', 'type' => 'text', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                ['name' => 'title', 'title' => 'VIP标题', 'type' => 'text', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
            ])
            ->help('当最近开通用户为空时，将显示虚拟开通用户')
            ->defaultValue($openUserDefaults);
        $builder->text('Member_VipTitle', 'VIP开通协议标题')->help('默认为 会员协议');
        $builder->richHtml('Member_VipContent', 'VIP开通说明')->help('默认为 VIP开通说明');
        $builder->formClass('wide');
        return $builder->perform();
    }
}
