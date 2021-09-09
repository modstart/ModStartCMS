<?php

namespace ModStart\Grid\Filter\Field;


class GroupTags extends AbstractFilterField
{
    protected $options = [];
    protected $serializeType = null;

    protected function setup()
    {
        $this->addFluentAttributeVariable('options');
        $this->addFluentAttributeVariable('serializeType');
    }

}
