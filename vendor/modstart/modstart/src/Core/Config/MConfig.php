<?php

namespace ModStart\Core\Config;


abstract class MConfig
{
    public abstract function get($key, $defaultValue = '', $useCache = true);

    public abstract function set($key, $value);

    public abstract function remove($key);

    public function getWithEnv($key, $defaultValue = null)
    {
        $value = config('env.CONFIG_' . $key);
        if (null === $value) {
            $value = $this->get($key);
        }
        if (empty($value)) {
            return $defaultValue;
        }
        return $value;
    }

    public function setArray($key, $value)
    {
        $this->set($key, json_encode($value));
    }

    public function getArray($key, $defaultValue = [], $useCache = true)
    {
        $value = $this->get($key, json_encode($defaultValue), $useCache);
        $value = @json_decode($value, true);
        if (!is_array($value) || empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function getBoolean($key, $defaultValue = false)
    {
        $value = $this->get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return $value ? true : false;
    }

    public function getInteger($key, $defaultValue = 0)
    {
        $value = $this->get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return intval($value);
    }

    public function getString($key, $defaultValue = '')
    {
        $value = $this->get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return '' . $value;
    }
}

