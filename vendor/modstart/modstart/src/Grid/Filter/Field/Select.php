<?php

namespace ModStart\Grid\Filter\Field;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Type\BaseType;

/**
 * Class Select
 * @package ModStart\Grid\Filter\Field
 *
 */
class Select extends AbstractFilterField
{
    protected $options = [];
    protected $selectSearch = false;

    protected function setup()
    {
        $this->addFluentAttributeVariable('options');
    }

    public function options($options = null)
    {
        if (null === $options) {
            return $this->options;
        }
        if (is_string($options) && is_subclass_of($options, BaseType::class)) {
            $value = $options::getList();
        } else if (is_array($options)) {
            $value = $options;
        } else {
            BizException::throws('Select options error');
        }
        $this->options = $value;
        return $this;
    }

    public function selectSearch($value = null)
    {
        if (null === $value) {
            return $this->selectSearch;
        }
        $this->setFluentAttribute('selectSearch', $value);
        return $this;
    }


}
