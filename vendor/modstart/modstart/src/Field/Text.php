<?php


namespace ModStart\Field;


use ModStart\Core\Util\StrUtil;

class Text extends AbstractField
{

    protected function setup()
    {
        $this->addVariables([
            'autoTrim' => false,
        ]);
    }

    /**
     * 保存时自动清除空格
     * @param bool $enable
     * @return $this
     */
    public function autoTrim($enable = true)
    {
        $this->addVariables(['autoTrim' => $enable]);
        return $this;
    }

    public function prepareInput($value, $dataSubmitted)
    {
        if ($this->variables['autoTrim']) {
            $value = StrUtil::filterSpecialChars($value);
            $value = trim($value);
        }
        return $value;
    }
}
