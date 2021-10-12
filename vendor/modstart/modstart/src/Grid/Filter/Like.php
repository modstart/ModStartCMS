<?php

namespace ModStart\Grid\Filter;

class Like extends AbstractFilter
{
    /**
     * Get condition of this filter.
     *
     * @param array $search
     *
     * @return array|mixed|void
     */
    public function condition($searchInfo)
    {
        if (isset($searchInfo['like']) && $searchInfo['like'] !== '') {
            return $this->buildCondition($this->column, 'like', "%${searchInfo['like']}%");
        }
        return null;
    }
}
