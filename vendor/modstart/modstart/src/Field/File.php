<?php


namespace ModStart\Field;


class File extends AbstractField
{
    protected $width = 80;

    protected function setup()
    {
        $this->addVariables([
            'uploadMode' => null,
            'server' => modstart_admin_url('data/file_manager/file'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    
    public function uploadMode($uploadMode)
    {
        $this->addVariables(['uploadMode' => $uploadMode]);
        return $this;
    }
}
