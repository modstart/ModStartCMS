<?php


namespace ModStart\Field;


class Link extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            'server' => modstart_admin_url('widget/link_select'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }
}
