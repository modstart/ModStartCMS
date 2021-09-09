<?php

namespace ModStart\Grid\Filter;

use ModStart\Grid\Filter\Field\Datetime;

class Range extends AbstractFilter
{
    public function condition($searchInfo)
    {
        if (isset($searchInfo['range']) && ($searchInfo['range']['min'] || $searchInfo['range']['max'])) {
            $value = $searchInfo['range'];
            if (!isset($value['min'])) {
                return $this->buildCondition($this->column, '<=', $value['max']);
            }
            if (!isset($value['max'])) {
                return $this->buildCondition($this->column, '>=', $value['min']);
            }
            $this->query = 'whereBetween';
            return $this->buildCondition($this->column, [$value['min'], $value['max']]);
        }
        return null;
    }

    public function datetime()
    {
        $this->field = new Field\Datetime($this);
        return $this;
    }

    public function date()
    {
        $this->field = new Field\Date($this);
        return $this;
    }

}
