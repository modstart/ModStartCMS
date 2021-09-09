<?php

namespace ModStart\Grid\Filter;

class Lt extends AbstractFilter
{
    
    public function condition($search)
    {
        $value = array_get($search, $this->column);

        if (is_null($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '<=', $this->value);
    }
}
