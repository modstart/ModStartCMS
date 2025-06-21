<?php


namespace ModStart\Field;


use ModStart\Core\Util\SerializeUtil;

class Values extends AbstractField
{
    protected $width = 300;
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
            'viewMode' => 'default',
            'countFixed' => false,
        ]);
    }

    /**
     * 设置内容填写模式，default=列表，mini=一行
     * @param $viewMode string default|mini|textarea
     * @return $this
     */
    public function viewMode($viewMode)
    {
        $this->addVariables(['viewMode' => $viewMode]);
        return $this;
    }

    /**
     * 是否固定显示数量
     * @param $enable bool
     * @return $this
     */
    public function countFixed($enable)
    {
        $this->addVariables(['countFixed' => $enable]);
        return $this;
    }

    public function viewModeMini()
    {
        return $this->viewMode('mini');
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }
}
