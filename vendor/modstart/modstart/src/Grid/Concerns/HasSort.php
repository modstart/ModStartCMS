<?php


namespace ModStart\Grid\Concerns;


trait HasSort
{
    public function canSort($value = null)
    {
        if (null === $value) {
            return $this->canSort;
        }
        $this->canSort = $value;
        return $this;
    }
}