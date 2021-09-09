<?php


namespace ModStart\Field;


class Code extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            'maxHeight' => '10em',
            'editorScripts' => '',
        ]);
    }

    public function maxHeight($value)
    {
        $this->addVariables(['maxHeight' => $value]);
        return $this;
    }

    public function editorScripts($value)
    {
        $this->addVariables(['editorScripts' => $value]);
        return $this;
    }
}
