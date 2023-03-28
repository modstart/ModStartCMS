<?php


namespace Module\VisitStatistic\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\ButtonDialogRequest;
use Module\VisitStatistic\Type\VisitStatisticDevice;

class VisitStatisticItemController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('visit_statistic_item')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->display('created_at', '时间')->listable(true);
                $builder->display('ip', 'IP')->listable(true);
                $builder->display('url', 'URL')->listable(true);
                $builder->type('device', '设备')->type(VisitStatisticDevice::class);

                $builder->display('ua', 'UA')->listable(true);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('ip', 'IP');
            })
            ->gridOperateAppend(
                ButtonDialogRequest::primary('设置', modstart_admin_url('visit_statistic/config'))
            )
            ->title('网站访问记录')
            ->canBatchDelete(true)
            ->canAdd(false)->canEdit(false)->canShow(false);
    }
}
