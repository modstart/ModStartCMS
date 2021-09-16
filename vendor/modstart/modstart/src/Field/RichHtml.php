<?php


namespace ModStart\Field;


class RichHtml extends AbstractField
{
    protected $listable = false;
    protected static $js = [
        'asset/common/editor.js',
    ];

    protected function setup()
    {
        $this->addVariables([
            'server' => modstart_admin_url('data/ueditor'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }
}
