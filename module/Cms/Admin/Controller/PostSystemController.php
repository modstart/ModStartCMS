<?php


namespace Module\Cms\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Tags;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Form\Type\FormMode;
use ModStart\Grid\GridFilter;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\TextLink;

class PostSystemController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('cms_post')
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->select('channelId', '频道')->optionModelTree('cms_channel');
                $builder->text('title', '标题')->hookRendering(function (AbstractField $field, $item, $index) {
                    switch ($field->renderMode()) {
                        case FieldRenderMode::GRID:
                            return AutoRenderedFieldValue::make(
                                TextLink::primary('<i class="iconfont icon-link"></i>', modstart_web_url('p/' . $item->alias), 'target="_blank"')
                                . ' ' . htmlspecialchars($item->title)
                            );
                    }
                });
                $builder->richHtml('contentHtml', '内容');
                $builder->switch('isRecommend', '推荐');
                $builder->switch('isOriginal', '原创');
                $builder->tags('tags', '标签')->serializeType(Tags::SERIALIZE_TYPE_COLON_SEPARATED);
                $builder->display('created_at', L('Created At'));
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->repositoryFilter(function (RepositoryFilter $filter) {
                $filter->where(['memberUserId' => 0]);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', '标题');
            })
            ->hookSaved(function (Form $form) {
                
                $item = $form->item();
                switch ($form->mode()) {
                    case FormMode::ADD:
                        ModelUtil::update('cms_post', $item->id, [
                            'alias' => RandomUtil::lowerString(16),
                            'memberUserId' => 0,
                            'memberPostId' => 0,
                            'wordCount' => HtmlUtil::workCount($item->contentHtml),
                            'isDeleted' => false,
                        ]);
                        break;
                    case FormMode::EDIT:
                        ModelUtil::update('cms_post', $item->id, [
                            'wordCount' => HtmlUtil::workCount($item->contentHtml),
                        ]);
                        break;
                }
            })
            ->title('系统文章');
    }
}
