<?php


namespace Module\ContentBlock\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\ContentBlock\Model\ContentBlock;
use Module\ContentBlock\Type\ContentBlockType;

class ContentBlockController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init(ContentBlock::class)
            ->field(function ($builder) {
                /** @var HasFields $builder */

                $builder->id('id', 'ID');
                $builder->text('name', '标识')->required();
                $builder->radio('type', '类型')
                    ->optionType(ContentBlockType::class)
                    ->defaultValue(ContentBlockType::IMAGE)
                    ->required()
                    ->when('=', ContentBlockType::IMAGE, function ($builder) {
                        /** @var HasFields $builder */
                        $builder->image('image', '封面');
                        $builder->text('title', '标题');
                        $builder->link('link', '链接');
                    })
                    ->when('=', ContentBlockType::HTML, function ($builder) {
                        /** @var HasFields $builder */
                        $builder->richHtml('content', '内容')->htmlFilter(false);
                    });

                $builder->display('_content', '内容')
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        return AutoRenderedFieldValue::makeView('module::ContentBlock.View.admin.content', [
                            'item' => $item
                        ]);
                    })
                    ->listable(true)->showable(false);

                $builder->number('sort', '排序')->defaultValue(999)->help('数字越小越靠前')->required();
                $builder->datetime('startTime', '开始时间')->help('留空表示不限制');
                $builder->datetime('endTime', '结束时间')->help('留空表示不限制');
                $builder->switch('enable', '启用')->defaultValue(true)->required();

                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->eq('type', '类型')->select(ContentBlockType::class);
                $filter->eq('name', '标识');
            })
            ->title('内容区块');
    }
}
