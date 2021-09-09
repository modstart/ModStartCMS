<?php


namespace ModStart\Support\Concern;



trait HasArguments
{
    private $arguments = [];

    
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

    
    public function getArgument($name, $default = null)
    {
        return isset($this->arguments[$name]) ? $this->arguments[$name] : $default;
    }
}