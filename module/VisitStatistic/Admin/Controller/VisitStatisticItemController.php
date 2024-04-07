<?php


namespace Module\VisitStatistic\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\VisitStatistic\Type\VisitStatisticDevice;

class VisitStatisticItemController extends Controller
{

    use HasAdminQuickCRUD;

    public static $PermitMethodMap = [
        'index' => '\\Module\\VisitStatistic\\Admin\\Controller\\VisitStatisticReportController@index'
    ];

    public function __construct()
    {
        $this->useGridDialogPage();
    }


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
            ->title('网站访问明细')
            ->canBatchDelete(true)
            ->canAdd(false)->canEdit(false)->canShow(false);
    }
}
