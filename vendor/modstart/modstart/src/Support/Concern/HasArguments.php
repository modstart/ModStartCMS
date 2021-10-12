<?php


namespace ModStart\Support\Concern;


/**
 * 支持自定义属性
 *
 * Trait HasArguments
 * @package ModStart\Support\Concern
 */
trait HasArguments
{
    private $arguments = [];

    /**
     * @param $name string|array
     * @param void
     */
    public function setArgument($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->arguments[$k] = $v;
            }
        } else {
            $this->arguments[$name] = $value;
        }
    }

    /**
     * @param $name
     * @param null $default
     * @return |null
     */
    public function getArgument($name, $default = null)
    {
        return isset($this->arguments[$name]) ? $this->arguments[$name] : $default;
    }
}