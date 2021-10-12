<?php


namespace ModStart\Support\Concern;

/**
 * Trait HasFluentAttribute
 * @package ModStart\Support\Concern
 * @method  $this urlExport($value = null)
 */
trait HasFluentAttribute
{
    protected function addFluentAttributeVariable($name)
    {
        if (!in_array($name, $this->fluentAttributes)) {
            $this->fluentAttributes[] = $name;
        }
    }

    protected function fluentAttributeVariables()
    {
        $data = [];
        foreach ($this->fluentAttributes as $v) {
            $data[$v] = $this->{$v};
        }
        return $data;
    }

    protected function isFluentAttribute($method)
    {
        return in_array($method, $this->fluentAttributes);
    }

    protected function setFluentAttribute($name, $value)
    {
        $this->{$name} = $value;
    }

    protected function fluentAttribute($method, $arguments)
    {
        if (!isset($arguments[0])) {
            return $this->{$method};
        }
        $this->{$method} = $arguments[0];
        return $this;
    }

}