<?php


namespace ModStart\Field;


class Icon extends AbstractField
{
    protected $width = 80;

    protected function setup()
    {
        $this->addVariables([
            'server' => modstart_admin_url('widget/icon'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }
}
