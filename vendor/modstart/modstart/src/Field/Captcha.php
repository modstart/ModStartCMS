<?php


namespace ModStart\Field;


class Captcha extends AbstractField
{
    protected $listable = false;
    protected $showable = false;

    protected function setup()
    {
        $this->addVariables([
            'url' => modstart_web_url('placeholder/300x800'),
        ]);
    }

    public function url($url)
    {
        $this->addVariables(['url' => $url]);
        return $this;
    }
}
