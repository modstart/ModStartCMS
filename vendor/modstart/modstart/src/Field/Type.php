<?php


namespace ModStart\Field;


use Illuminate\Support\Str;
use ModStart\Core\Type\TypeUtil;
use ModStart\Core\Util\ConstantUtil;

class Type extends AbstractField
{
    protected function setup()
    {
        $this->addVariables([
            /**
             * @var string|array
             */
            'valueMap' => null,
            'colorMap' => [],
        ]);
    }


    /**
     * @param string|array|\Closure $value
     * @param null|array $colorMap 设置颜色，空数组自动猜测模式，null不启用
     * @return $this
     */
    public function type($value, $colorMap = [])
    {
        if ($value instanceof \Closure) {
            $value = call_user_func($value, $this);
        } else if (is_array($value)) {
        } else {
            if (null !== $colorMap && is_array($colorMap) && empty($colorMap)) {
                $colorMap = TypeUtil::colorGuessMap($value);
            }
            $value = $value::getList();
        }
        $this->addVariables([
            'valueMap' => $value,
            'colorMap' => $colorMap,
        ]);
        return $this;
    }
}