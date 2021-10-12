<?php

namespace ModStart\Grid\Filter;

class Lt extends AbstractFilter
{
    /**
     * Get condition of this filter.
     *
     * @param array $search
     *
     * @return array|mixed|void
     */
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
