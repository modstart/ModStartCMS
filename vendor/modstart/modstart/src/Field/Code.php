<?php


namespace ModStart\Field;


class Code extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            'maxHeight' => '10em',
            'editorHeight' => '3rem',
            'editorScripts' => '',
            'language' => 'text',
        ]);
    }

    public function maxHeight($value)
    {
        $this->addVariables(['maxHeight' => $value]);
        return $this;
    }

    public function editorHeight($value)
    {
        $this->addVariables(['editorHeight' => $value]);
        return $this;
    }

    public function editorScripts($value)
    {
        $this->addVariables(['editorScripts' => $value]);
        return $this;
    }

    /**
     * 设置编辑的语言
     * @param $value string 语言，目已支持 text,sh,json,html
     * @return $this
     */
    public function language($value)
    {
        $this->addVariables(['language' => $value]);
        return $this;
    }
}
