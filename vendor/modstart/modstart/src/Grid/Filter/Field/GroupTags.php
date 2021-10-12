<?php

namespace ModStart\Grid\Filter\Field;

/**
 * Class GroupTags
 * @package ModStart\Grid\Filter\Field
 *
 * @method GroupTags|mixed options($value = null)
 * @method GroupTags|mixed serializeType($value = null)
 */
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
