<?php


namespace ModStart\Field;


class Image extends AbstractField
{
    const MODE_UPLOAD_DIRECT = 'uploadDirect';
    const MODE_UPLOAD_DIRECT_RAW = 'uploadDirectRaw';

    protected $width = 80;

    protected function setup()
    {
        $this->addVariables([
            'uploadMode' => null,
            'server' => modstart_admin_url('data/file_manager/image'),
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
