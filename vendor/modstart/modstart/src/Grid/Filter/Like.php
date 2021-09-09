<?php

namespace ModStart\Grid\Filter;

class Like extends AbstractFilter
{
    
    public function condition($searchInfo)
    {
        if (isset($searchInfo['like']) && $searchInfo['like'] !== '') {
            return $this->buildCondition($this->column, 'like', "%${searchInfo['like']}%");
        }
        return null;
    }
}
