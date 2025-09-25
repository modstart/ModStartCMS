<?php


namespace Module\VisitStatistic\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Type\TypeUtil;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Type\Gender;
use Module\Vendor\QuickRun\Export\ExportHandle;
use Module\VisitStatistic\Model\VisitStatisticItem;
use Module\VisitStatistic\Type\VisitStatisticDevice;

class VisitStatisticItemController extends Controller
{

    use HasAdminQuickCRUD;

    public static $PermitMethodMap = [
        '*' => '\\Module\\VisitStatistic\\Admin\\Controller\\VisitStatisticReportController@index'
    ];

    public function __construct()
    {
        $this->useGridDialogPage();
    }


    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init(VisitStatisticItem::class)
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
            ->canExport(true)
            ->canAdd(false)->canEdit(false)->canShow(false);
    }

    public function export(ExportHandle $handle)
    {
        $headTitles = [
            'ID', '时间', 'IP', 'URL', '设备', 'UA',
        ];
        return $handle
            ->withPageTitle('导出访问明细')
            ->withDefaultExportName('访问明细')
            ->withHeadTitles($headTitles)
            ->handleFetch(function ($page, $pageSize, $search, $param) {
                $query = ModelUtil::model(VisitStatisticItem::class);
                $query = $query->orderBy('id', 'desc');
                foreach ($search as $searchItem) {
                    if (!empty($searchItem['ip']['eq'])) {
                        $query = $query->where('ip', $searchItem['ip']['eq']);
                    }
                }
                $result = $query->paginate($pageSize, ['*'], 'page', $page)->toArray();
                $list = [];
                foreach ($result['data'] as $item) {
                    $one = [];
                    $one[] = $item['id'];
                    $one[] = $item['created_at'];
                    $one[] = $item['ip'];
                    $one[] = $item['url'];
                    $one[] = TypeUtil::name(VisitStatisticDevice::class, $item['device']);
                    $one[] = $item['ua'];
                    $list[] = $one;
                }
                return [
                    'list' => $list,
                    'total' => $result['total'],
                ];
            })
            ->performCommon();
    }
}
