<?php


namespace ModStart\Field;


class Textarea extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            'autoHeight' => false,
            'autoHeightMin' => 300,
        ]);
    }

    public function autoHeight($value, $autoHeightMin = 300)
    {
        $this->addVariables([
            'autoHeight' => $value,
            'autoHeightMin' => $autoHeightMin,
        ]);
        return $this;
    }
}
