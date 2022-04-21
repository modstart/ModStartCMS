<?php


namespace ModStart\Grid\Filter;


class Has extends AbstractFilter
{
    protected $query = 'whereIn';

    /**
     * Get condition of this filter.
     *
     * @param array $search
     *
     * @return array|mixed|void
     */
    public function condition($searchInfo)
    {
        if (isset($searchInfo['has']) && is_array($searchInfo['has'])) {
            return $this->buildCondition($this->column, $searchInfo['has']);
        }
        return null;
    }

    /**
     * @param mixed $options array | BaseType
     * @return $this
     */
    public function checkbox($options)
    {
        $this->field = new Field\Checkbox($this);
        $this->field->options($options);
        return $this;
    }
}
