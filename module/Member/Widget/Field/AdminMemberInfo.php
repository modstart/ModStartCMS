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
    protected $editable = false;

    protected function setup()
    {
        parent::setup();
        $this->addVariables([
            'memberFieldName' => null,
            'formAsDisplay' => null,
        ]);
    }

    public function memberFieldName($v)
    {
        $this->addVariables(['memberFieldName' => $v]);
        return $this;
    }

    public function formAsDisplay($v)
    {
        $this->addVariables(['formAsDisplay' => $v]);
        return $this;
    }

    public function renderView(AbstractField $field, $item, $index = 0)
    {
        switch ($field->renderMode()) {
            case FieldRenderMode::GRID:
            case FieldRenderMode::DETAIL:
                $this->renderAsDisplay();
                break;
            case FieldRenderMode::FORM:
                if ($this->getVariable('formAsDisplay')) {
                    $this->renderAsDisplay();
                }
                break;
        }
        return parent::renderView($field, $item, $index);
    }

    private function renderAsDisplay()
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) {
            $column = $field->column();
            return MemberCmsUtil::showFromId($item->{$column}, $this->getVariable('memberFieldName'));
        });
    }
}
