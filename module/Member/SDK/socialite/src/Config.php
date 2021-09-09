<?php

namespace Overtrue\Socialite;

use ArrayAccess;
use InvalidArgumentException;

class Config implements ArrayAccess, \JsonSerializable
{
    
    protected $config;

    
    public function __construct($config)
    {
        $this->config = $config;
    }

    
    public function get($key, $default = null)
    {
        $config = $this->config;

        if (is_null($key)) {
            return $config;
        }

        if (isset($config[$key])) {
            return $config[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    
    public function set($key, $value)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException('Invalid config key.');
        }

        $keys = explode('.', $key);
        $config = &$this->config;

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($config[$key]) || !is_array($config[$key])) {
                $config[$key] = [];
            }
            $config = &$config[$key];
        }

        $config[array_shift($keys)] = $value;

        return $config;
    }

    
    public function has($key)
    {
        return (bool) $this->get($key);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    public function jsonSerialize()
    {
        return $this->config;
    }

    public function __toString()
    {
        return \json_encode($this, \JSON_UNESCAPED_UNICODE);
    }
}
