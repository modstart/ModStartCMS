<?php


namespace Module\Nav\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Nav\Type\NavPosition;
use Module\Nav\Util\NavUtil;

class NavController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('nav')
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->select('position', '位置')->optionType(NavPosition::class);
                $builder->text('name', '名称');
                $builder->link('link', '链接');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('position', '位置')->select(NavPosition::class);
            })
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->title('导航')
            ->hookSaved(function (Form $form) {
                NavUtil::clearCache();
            });
    }
}
