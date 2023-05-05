<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Type\TypeUtil;
use ModStart\Field\AbstractField;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Util\MemberCmsUtil;
use Module\Member\Util\MemberVipUtil;
use Module\Vendor\Type\OrderStatus;

class MemberVipOrderController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_vip_order')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->display('id', '业务订单ID');
                $builder->datetime('created_at', '创建时间');
                $builder->display('memberUserId', '用户')->hookRendering(function (AbstractField $field, $item, $index) {
                    return MemberCmsUtil::showFromId($item->memberUserId);
                });
                $builder->display('type', '订单类型');
                $builder->display('vipId', 'VIP')->hookRendering(function (AbstractField $field, $item, $index) {
                    return MemberVipUtil::get($item->vipId, 'title');
                });
                $builder->display('payFee', '支付金额');
                $builder->type('status', '状态')->type(OrderStatus::class);
                $builder->display('expire', '执行后会员过期时间')->width(200);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', '业务订单ID');
                $filter->eq('memberUserId', '用户ID');
                $filter->eq('vipId', 'VIP')->select(MemberVipUtil::mapTitle());
                $filter->eq('status', '状态')->select([
                    OrderStatus::WAIT_PAY => TypeUtil::name(OrderStatus::class, OrderStatus::WAIT_PAY),
                    OrderStatus::COMPLETED => TypeUtil::name(OrderStatus::class, OrderStatus::COMPLETED),
                ]);
            })
            ->disableCUD()->canShow(false)
            ->title('用户-VIP订单');
    }
}
