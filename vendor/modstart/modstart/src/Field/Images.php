<?php


namespace ModStart\Field;


use ModStart\Core\Util\ConvertUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\ModStart;

class Images extends AbstractField
{
    protected $width = 80;

    protected function setup()
    {
        ModStart::lang(['CompressingImage']);
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
        if (null === $value) {
            return $value;
        }
        return ConvertUtil::toArray($value);
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        return ConvertUtil::toArray($value);
    }
}
