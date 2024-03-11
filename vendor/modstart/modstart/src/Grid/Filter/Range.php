<?php

namespace ModStart\Grid\Filter;

use ModStart\Grid\Filter\Field\Datetime;

class Range extends AbstractFilter
{
    public function condition($searchInfo)
    {
        if (!empty($searchInfo['range']) && (!empty($searchInfo['range']['min']) || !empty($searchInfo['range']['max']))) {
            $value = $searchInfo['range'];
            if (empty($value['min'])) {
                return $this->buildCondition($this->column, '<=', $value['max']);
            }
            if (empty($value['max'])) {
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

    public function text()
    {
        $this->field = new Field\Text($this);
        return $this;
    }

    public function number()
    {
        $this->field = new Field\Number($this);
        return $this;
    }

}
