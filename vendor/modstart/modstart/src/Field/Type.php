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
            
            'valueMap' => null,
            'colorMap' => [],
        ]);
    }


    
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