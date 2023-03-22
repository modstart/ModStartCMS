<?php


namespace Module\Article\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Field\AbstractField;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\TextLink;
use Module\Article\Type\ArticlePosition;
use Module\Article\Util\ArticleUtil;

class ArticleController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('article')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->select('position', '位置')->optionType(ArticlePosition::class);
                $builder->text('title', '名称')
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        switch ($field->renderMode()) {
                            case FieldRenderMode::GRID:
                            case FieldRenderMode::DETAIL:
                                return TextLink::primary(
                                    htmlspecialchars($item->title),
                                    ArticleUtil::url($item->toArray()),
                                    'target="_blank"'
                                );
                        }
                    });
                $builder->text('alias', '别名')->help('默认留空，可通过链接 /article/别名 访问');
                $builder->richHtml('content', '内容')->listable(false);
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('position', '位置')->select(ArticlePosition::class);
                $filter->like('title', L('Title'));
            })
            ->title('通用文章')
            ->hookSaved(function (Form $form) {
                ArticleUtil::clearCache();
            });
    }
}
