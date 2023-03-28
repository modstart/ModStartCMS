<?php


namespace ModStart\Field;


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
                // ['name' => 'xxx', 'title' => '开关', 'type' => 'switch', 'defaultValue' => false],
                // ['name' => 'xxx', 'title' => '文本', 'type' => 'text', 'defaultValue' => ''],
                // ['name' => 'xxx', 'title' => '图标', 'type' => 'icon', 'defaultValue' => 'iconfont icon-home'],
                // ['name' => 'xxx', 'title' => '数字', 'type' => 'number', 'defaultValue' => 0],
            ],
            'valueItem' => new \stdClass(),
            'iconServer' => modstart_admin_url('widget/icon'),
        ]);
    }

    private function getValueItem()
    {
        $fields = $this->getVariable('fields');
        $valueItem = [];
        foreach ($fields as $f) {
            $valueItem[$f['name']] = isset($f['defaultValue']) ? $f['defaultValue'] : null;
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
