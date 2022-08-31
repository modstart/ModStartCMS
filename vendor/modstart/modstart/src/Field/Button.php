<?php


namespace ModStart\Field;


class Button extends AbstractField
{
    protected $listable = false;
    protected $showable = false;

    protected function setup()
    {
        $this->addVariables([
            'type' => 'button',
            'style' => 'default',
        ]);
    }

    public function type($value)
    {
        $this->addVariables(['type' => $value]);
        return $this;
    }

    public function style($value)
    {
        $this->addVariables(['style' => $value]);
        return $this;
    }

    public function forSubmit()
    {
        $this->addVariables([
            'type' => 'submit',
            'style' => 'primary',
        ]);
    }
}
