<?php


namespace ModStart\Field;


use ModStart\Core\Exception\BizException;

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
                // ['name' => 'xxx1', 'title' => '开关', 'type' => 'switch', 'defaultValue' => false, 'placeholder'=>'', ],
                // ['name' => 'xxx2', 'title' => '文本', 'type' => 'text', 'defaultValue' => '', 'placeholder'=>'', ],
                // ['name' => 'xxx3', 'title' => '图标', 'type' => 'icon', 'defaultValue' => 'iconfont icon-home', 'placeholder'=>'', ],
                // ['name' => 'xxx4', 'title' => '数字', 'type' => 'number', 'defaultValue' => 0, 'placeholder'=>'', ],
                // ['name' => 'xxx5', 'title' => '数字', 'type' => 'number-text', 'defaultValue' => 0, 'placeholder'=>'', ],
            ],
            'valueItem' => new \stdClass(),
            'iconServer' => modstart_admin_url('widget/icon'),
            'hasIcon' => false,
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

    public function fields($value)
    {
        $this->addVariables(['fields' => $value]);
        $this->addVariables(['valueItem' => $this->getValueItem()]);
        $nameMap = [];
        foreach ($value as $f) {
            BizException::throwsIf('ComplexFieldsList.字段名重复 - ' . $f['name'], isset($nameMap[$f['name']]));
            $nameMap[$f['name']] = true;
            if ($f['type'] == 'icon') {
                $this->addVariables(['hasIcon' => true]);
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
        return json_encode($value, JSON_UNESCAPED_UNICODE);
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
