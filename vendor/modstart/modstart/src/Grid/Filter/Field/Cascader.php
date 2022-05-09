<?php

namespace ModStart\Grid\Filter\Field;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Type\BaseType;

/**
 * Class Select
 * @package ModStart\Grid\Filter\Field
 *
 */
class Cascader extends AbstractFilterField
{
    protected $nodes = [];

    protected function setup()
    {
        $this->addFluentAttributeVariable('nodes');
    }

    public function nodes($nodes = null)
    {
        if (null === $nodes) {
            return $this->nodes;
        }
        if (is_string($nodes) && is_subclass_of($nodes, BaseType::class)) {
            $value = $nodes::getList();
        } else if (is_array($nodes)) {
            $value = $nodes;
        } else {
            BizException::throws('Select nodes error');
        }
        $this->nodes = $value;
    }
}
