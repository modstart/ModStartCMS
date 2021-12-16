<?php


namespace Module\Member\Widget\Field;


use ModStart\Field\AbstractField;
use ModStart\Field\Text;
use ModStart\Field\Type\FieldRenderMode;
use Module\Member\Util\MemberCmsUtil;

/**
 * 后台管理用户信息字段
 *
 * Class AdminMemberInfo
 * @package Module\Member\Widget\Field
 */
class AdminMemberInfo extends Text
{
    protected $view = 'modstart::core.field.text';

    public function renderView(AbstractField $field, $item, $index = 0)
    {
        switch ($field->renderMode()) {
            case FieldRenderMode::GRID:
            case FieldRenderMode::DETAIL:
                $this->hookRendering(function (AbstractField $field, $item, $index) {
                    $column = $field->column();
                    return MemberCmsUtil::showFromId($item->{$column});
                });
        }
        return parent::renderView($field, $item, $index);
    }
}
