<?php


namespace ModStart\Field;


class Raw extends AbstractField
{
    public function renderView(AbstractField $field, $item, $index = 0)
    {
        return $this->value();
    }

}
