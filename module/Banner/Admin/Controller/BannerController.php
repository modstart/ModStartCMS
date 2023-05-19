<?php


namespace Module\Banner\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Banner\Type\BannerPosition;
use Module\Banner\Type\BannerPositionRemark;
use Module\Banner\Type\BannerType;
use Module\Banner\Util\BannerUtil;

class BannerController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('banner')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->select('position', '位置')->optionType(BannerPositionRemark::class);
                $builder->image('image', '图片');
                $builder->radio('type', '样式类型')
                    ->optionType(BannerType::class)
                    ->when(BannerType::IMAGE_TITLE_SLOGAN_LINK, function ($context) {
                        /** @var HasFields $context */
                        $context->text('title', '标题');
                        $context->textarea('slogan', '内容描述');
                        $context->text('linkText', '链接文字');
                        $context->switch('colorReverse', '颜色反转');
                    })
                    ->when(BannerType::VIDEO, function ($context) {
                        /** @var HasFields $context */
                        $context->video('video', '视频');
                    })->required();
                $builder->color('backgroundColor', '背景色');
                $builder->link('link', '链接');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('position', '位置')->select(BannerPosition::class);
            })
            ->hookChanged(function (Form $form) {
                BannerUtil::clearCache();
            })
            ->canBatchDelete(true)
            ->enablePagination(false)
            ->defaultOrder(['sort', 'asc'])
            ->canSort(true)
            ->title('轮播图片');
    }
}
