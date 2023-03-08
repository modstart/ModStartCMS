<?php


namespace ModStart\Core\Input;


use ModStart\Core\Util\StrUtil;

class ArrayPackage
{
    private $data;
    private $cursor = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function build($data)
    {
        $package = new ArrayPackage($data);
        $package->cursor = 0;
        return $package;
    }

    public function resetCursor()
    {
        $this->cursor = 0;
    }

    public function next($defaultValue = null)
    {
        $index = $this->cursor;
        $this->cursor++;
        if (isset($this->data[$index])) {
            return $this->data[$index];
        }
        return $defaultValue;
    }

    public function nextTrimString($defaultValue = null)
    {
        $value = $this->next($defaultValue);
        if (null === $value) {
            return $defaultValue;
        }
        $value = @trim((string)$value);
        $value = StrUtil::filterSpecialChars($value);
        return $value;
    }

    public function nextTrimStringMapInteger($stringMap = [], $defaultValue = null)
    {
        $value = $this->nextTrimString();
        if (isset($stringMap[$value])) {
            return $stringMap[$value];
        }
        return $defaultValue;
    }

    public function nextType($typeCls, $defaultValue = null)
    {
        $value = $this->nextTrimString();
        $list = $typeCls::getList();
        foreach ($list as $k => $v) {
            if ($value == $k) {
                return $k;
            }
        }
        return $defaultValue;
    }

    public function nextTypeValue($typeCls, $defaultValue = null)
    {
        $value = $this->nextTrimString();
        $list = $typeCls::getList();
        foreach ($list as $k => $v) {
            if ($value == $k) {
                return $v;
            }
        }
        return $defaultValue;
    }

    public function nextInteger($defaultValue = null)
    {
        $value = $this->next($defaultValue);
        if (null === $value) {
            return $value;
        }
        return intval($value);
    }
}
