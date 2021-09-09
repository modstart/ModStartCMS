<?php


namespace Module\Partner\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Partner\Type\PartnerPosition;
use Module\Partner\Util\PartnerUtil;

class PartnerController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('partner')
            ->field(function ($builder) {
                
                $builder->id('id','ID');
                $builder->select('position', '位置')->optionType(PartnerPosition::class);
                $builder->text('title', '名称');
                $builder->image('logo', 'Logo');
                $builder->text('link', '链接');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('position', '位置')->select(PartnerPosition::class);
                $filter->like('title', L('Title'));
            })
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->title('友情链接')
            ->hookSaved(function (Form $form) {
                PartnerUtil::clearCache();
            });
    }
}
