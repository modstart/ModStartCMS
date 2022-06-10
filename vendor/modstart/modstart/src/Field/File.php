<?php


namespace ModStart\Field;


class File extends AbstractField
{
    const MODE_DEFAULT = 'default';
    const MODE_RAW = 'raw';

    protected function setup()
    {
        $this->addVariables([
            'mode' => self::MODE_DEFAULT,
            'server' => modstart_admin_url('data/file_manager/file'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    /**
     * 上传模式
     * Image::MODE_RAW：直接点选文件并上传到系统中，用户文件管理中不可见
     * Image::MODE_DEFAULT：正常上传模式
     * @param $mode
     * @return $this
     */
    public function mode($mode)
    {
        $this->addVariables(['mode' => $mode]);
        return $this;
    }
}
