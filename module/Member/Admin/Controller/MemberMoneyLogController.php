<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Util\MemberCmsUtil;

class MemberMoneyLogController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_money_log')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->display('memberUserId', '用户')->hookRendering(function (AbstractField $field, $item, $index) {
                    return MemberCmsUtil::showFromId($item->memberUserId);
                });
                $builder->display('change', '金额')
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        return AutoRenderedFieldValue::make(
                            $item->change > 0 ?
                                '<span class="ub-text-success">+' . $item->change . '</span>' :
                                '<span class="ub-text-danger">' . $item->change . '</span>'
                        );
                    });
                $builder->text('remark', '备注');
                $builder->display('created_at', L('Created At'));
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('memberUserId', '用户ID');
            })
            ->title('用户钱包流水')->canAdd(false)->canEdit(false)->canDelete(false);
    }

}
