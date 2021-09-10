<?php


namespace ModStart\Field;


class File extends AbstractField
{
    const MODE_DEFAULT = 'default';
    const MODE_RAW = 'raw';

    protected $width = 80;

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

    
    public function mode($mode)
    {
        $this->addVariables(['mode' => $mode]);
        return $this;
    }
}
