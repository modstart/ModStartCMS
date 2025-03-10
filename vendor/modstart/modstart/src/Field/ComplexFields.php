<?php


namespace ModStart\Field;


use ModStart\Core\Util\SerializeUtil;

/**
 * Json多组键值对字段
 * Class ComplexFields
 * @package ModStart\Field
 */
class ComplexFields extends AbstractField
{
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'fields' => [
                // ['name' => 'xxx', 'title' => '开关', 'type' => 'switch', 'defaultValue' => false, 'tip'=>'xxx', ],
                // ['name' => 'xxx', 'title' => '文本', 'type' => 'text', 'defaultValue' => '', 'tip'=>'xxx', ],
                // ['name' => 'xxx', 'title' => '图标', 'type' => 'icon', 'defaultValue' => 'iconfont icon-home', 'tip'=>'xxx', ],
                // ['name' => 'xxx', 'title' => '数字', 'type' => 'number', 'defaultValue' => 0, 'tip'=>'xxx', ],
                // ['name' => 'xxx', 'title' => '数字', 'type' => 'slider', 'defaultValue' => 0, 'min' => 1, 'max' => 5, 'step' => 1, 'tip'=>'xxx', ],
                // ['name' => 'xxx', 'title' => '链接', 'type' => 'link', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                // ['name' => 'xxx', 'title' => '图片', 'type' => 'image', 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', ],
                // ['name' => 'xxx', 'title' => '下拉', 'type' => 'select', 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', 'option'=>[ 'a'=>'A', 'b'=>'B', ] ],
                // ['name' => 'xxx', 'title' => '单选', 'type' => 'radio', 'defaultValue' => '', 'placeholder'=>'', 'tip'=>'', 'option'=>[ 'a'=>'A', 'b'=>'B', ] ],
            ],
            'iconServer' => modstart_admin_url('widget/icon'),
            'linkServer' => modstart_admin_url('widget/link_select'),
        ]);
    }

    private function getDefaultValues()
    {
        $fields = $this->getVariable('fields');
        $defaultValue = [];
        foreach ($fields as $f) {
            $defaultValue[$f['name']] = isset($f['defaultValue']) ? $f['defaultValue'] : null;
        }
        return $defaultValue;
    }

    public function iconServer($server)
    {
        $this->addVariables(['iconServer' => $server]);
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
        $this->defaultValue($this->getDefaultValues());
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = new \stdClass();
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
            $value = new \stdClass();
        }
        return $value;
    }
}
