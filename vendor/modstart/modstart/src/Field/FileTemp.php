<?php


namespace ModStart\Field;


use ModStart\Core\Input\Response;
use ModStart\Data\DataManager;

class FileTemp extends AbstractField
{
    protected $width = 80;
    protected static $js = ['asset/common/uploadButton.js'];

    protected function setup()
    {
        $this->addVariables([
            'autoSave' => true,
            'server' => modstart_admin_url('data/file_manager/file'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    public function autoSave($value)
    {
        $this->addVariables(['autoSave' => $value]);
        return $this;
    }

    public function prepareInput($value, $model)
    {
        if (!empty($this->variables['autoSave']) && DataManager::isDataTemp($value)) {
            $ret = DataManager::storeTempDataByPath($value);
            if (Response::isError($ret)) {
                return $value;
            }
            return DataManager::fix($ret['data']['path']);
        }
        return $value;
    }
}
