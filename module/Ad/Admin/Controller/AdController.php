<?php


namespace Module\Ad\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Ad\Type\AdPosition;
use Module\Ad\Util\AdUtil;

class AdController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('ad')
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->select('position', '位置')->optionType(AdPosition::class);
                $builder->image('image', '图片');
                $builder->link('link', '链接');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('position', '位置')->select(AdPosition::class);
            })
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->title('广告位')
            ->dialogSizeSmall()
            ->hookSaved(function (Form $form) {
                AdUtil::clearCache();
            });
    }
}
