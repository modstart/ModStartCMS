<?php


namespace ModStart\Field;


use ModStart\Core\Util\ConvertUtil;

class Images extends AbstractField
{
    protected $width = 80;

    protected function setup()
    {
        $this->addVariables([
            'server' => modstart_admin_url('data/file_manager/image'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        return ConvertUtil::toArray($value);
    }

    public function serializeValue($value, $model)
    {
        return json_encode($value);
    }

    public function prepareInput($value, $model)
    {
        return ConvertUtil::toArray($value);
    }
}
