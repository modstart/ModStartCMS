<?php


namespace ModStart\Support\Concern;


/**
 * 支持自定义属性
 *
 * Trait HasVariables
 * @package ModStart\Support\Concern
 */
trait HasVariables
{
    protected $variables = [];

    public function varaibles()
    {
        return $this->variables;
    }

    /**
     * @param $name string|array
     * @param void
     */
    public function setVariable($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->variables[$k] = $v;
            }
        } else {
            $this->variables[$name] = $value;
        }
    }

    /**
     * @param $name
     * @param null $default
     * @return |null
     */
    public function getVariable($name, $default = null)
    {
        return isset($this->variables[$name]) ? $this->variables[$name] : $default;
    }
}
