<?php


namespace ModStart\Field;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Field\Type\ComplexFieldsType;
use ModStart\ModStart;

/**
 * Json多组键值对字段
 * Class ComplexFields
 * @package ModStart\Field
 */
class ComplexFieldsList extends AbstractField
{
    protected $value = [];
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'fields' => [
                // 同步修改 ComplexFields 中的注释
                // ['name' => 'xxx', 'title' => '文本（只读）', 'type' => ComplexFieldsType::TYPE_DISPLAY, 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '开关', 'type' => ComplexFieldsType::TYPE_SWITCH, 'defaultValue' => false, 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '单行文本', 'type' => ComplexFieldsType::TYPE_TEXT, 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '多行文本', 'type' => ComplexFieldsType::TYPE_TEXTAREA, 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '图标', 'type' => ComplexFieldsType::TYPE_ICON, 'defaultValue' => 'iconfont icon-home', 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '图片', 'type' => ComplexFieldsType::TYPE_IMAGE, 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '多字符串值', 'type' => ComplexFieldsType::TYPE_VALUES, 'defaultValue' => 0, 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '数字', 'type' => ComplexFieldsType::TYPE_NUMBER, 'defaultValue' => 0, 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '数字', 'type' => ComplexFieldsType::TYPE_NUMBER_TEXT, 'defaultValue' => 0, 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '滑动数字', 'type' => ComplexFieldsType::TYPE_SLIDER, 'min' => 1, 'max' => 5, 'step' => 1, 'defaultValue' => 0, 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '链接', 'type' => ComplexFieldsType::TYPE_LINK, 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                // ['name' => 'xxx', 'title' => '单选', 'type' => ComplexFieldsType::TYPE_SELECT, 'option' => ['a'=>'aa','b'=>'bb'], 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                // ['name' => 'xxx', 'title' => '单选按钮', 'type' => ComplexFieldsType::TYPE_RADIO, 'option' => ['a'=>'aa','b'=>'bb'], 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                // ['name' => 'xxx', 'title' => '颜色', 'type' => ComplexFieldsType::TYPE_COLOR, 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                // ['name' => 'xxx', 'title' => '富文本', 'type' => ComplexFieldsType::TYPE_RICH_HTML, 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', ],
            ],
            'valueItem' => new \stdClass(),
            'iconServer' => modstart_admin_url('widget/icon'),
            'iconGroups' => ['iconfont', 'font-awesome'],
            'linkServer' => modstart_admin_url('widget/link_select'),
            '_hasIcon' => false,
            'itemActions' => ['sort', 'copy', 'delete', 'add'],
            'itemCanAdd' => true,
        ]);
    }

    private function getValueItem()
    {
        $fields = $this->getVariable('fields');
        $valueItem = [];
        foreach ($fields as $f) {
            $valueItem[$f['name']] = isset($f['defaultValue']) ? $f['defaultValue'] : null;
        }
        if (empty($valueItem)) {
            $valueItem = new \stdClass();
        }
        return $valueItem;
    }

    public function iconServer($server)
    {
        $this->addVariables(['iconServer' => $server]);
        return $this;
    }

    public function itemActions($value)
    {
        $this->addVariables(['itemActions' => $value]);
        return $this;
    }

    public function itemCanAdd($value)
    {
        $this->addVariables(['itemCanAdd' => $value]);
        return $this;
    }

    /**
     * 指定图标组
     * @param $iconGroups array 图标组 iconfont font-awesome
     * @return $this
     */
    public function iconGroups($iconGroups)
    {
        $this->addVariables(['iconGroups' => $iconGroups]);
        return $this;
    }

    public function linkServer($server)
    {
        $this->addVariables(['linkServer' => $server]);
        return $this;
    }

    public function fields($value)
    {
        $this->addVariables(['fields' => $value]);
        $this->addVariables(['valueItem' => $this->getValueItem()]);
        $nameMap = [];
        foreach ($value as $f) {
            BizException::throwsIf('ComplexFieldsList.字段名重复 - ' . $f['name'], isset($nameMap[$f['name']]));
            $nameMap[$f['name']] = true;
            if ($f['type'] == ComplexFieldsType::TYPE_ICON) {
                $this->addVariables(['_hasIcon' => true]);
            } else if ($f['type'] === ComplexFieldsType::TYPE_RICH_HTML) {
                ModStart::js('asset/common/editor.js');
            }
        }
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }
}
