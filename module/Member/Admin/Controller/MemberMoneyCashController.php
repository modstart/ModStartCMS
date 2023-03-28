<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Field\AbstractField;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\TextAjaxRequest;
use Module\Member\Type\MemberMoneyCashStatus;
use Module\Member\Type\MemberMoneyCashType;
use Module\Member\Util\MemberCmsUtil;
use Module\Member\Util\MemberVipUtil;

class MemberMoneyCashController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_money_cash')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->display('memberUserId', '用户')->hookRendering(function (AbstractField $field, $item, $index) {
                    return MemberCmsUtil::showFromId($item->memberUserId);
                });
                $builder->type('status', '状态')->type(MemberMoneyCashStatus::class);
                $builder->text('money', '金额');
                $builder->text('moneyAfterTax', '到账金额');
                $builder->text('remark', '备注');
                $builder->type('type', '账号类型')->type(MemberMoneyCashType::class);
                $builder->text('realname', '姓名');
                $builder->text('account', '账号');
                $builder->display('created_at', L('Created At'));
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('status', '状态')->radio(MemberMoneyCashStatus::class);
            })
            ->hookItemOperateRendering(function (ItemOperate $itemOperate) {
                $item = $itemOperate->item();
                switch ($item->status) {
                    case MemberMoneyCashStatus::VERIFYING:
                        $itemOperate->prepend(TextAjaxRequest::primary('已汇款', action('\\' . __CLASS__ . '@pass', ['_id' => $item->id])));
                        break;
                }
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('memberUserId', '用户ID');
            })
            ->title('用户钱包提现申请')->canAdd(false)->canEdit(false)->canDelete(false);
    }

    public function pass()
    {
        AdminPermission::demoCheck();
        $id = CRUDUtil::id();
        ModelUtil::update('member_money_cash', $id, ['status' => MemberMoneyCashStatus::SUCCESS]);
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }
}
