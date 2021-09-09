<?php

namespace ModStart\Core\Util;


class ArrayUtil
{
    
    public static function sequenceEqual($arr1, $arr2)
    {
        sort($arr1);
        sort($arr2);
        return json_encode($arr1) == json_encode($arr2);
    }

    
    public static function equal($arr1, $arr2, $keys = null, $strict = false)
    {
        if (null === $keys) {
            $keys = array_merge(array_keys($arr1), array_keys($arr2));
        }
        foreach ($keys as $k) {
            if (!array_key_exists($k, $arr1)) {
                return false;
            }
            if (!array_key_exists($k, $arr2)) {
                return false;
            }
            if ($strict) {
                if ($arr1[$k] !== $arr2[$k]) {
                    return false;
                }
            } else {
                if ($arr1[$k] != $arr2[$k]) {
                    return false;
                }
            }
        }
        return true;
    }

    
    public static function flatItemsByKey(&$records, $key)
    {
        $result = [];
        foreach ($records as $item) {
            $result[] = $item[$key];
        }
        return $result;
    }

    
    public static function keepKeys($record, $keys)
    {
        if (empty($record)) {
            return $record;
        }
        if (is_string($keys)) {
            $keys = [$keys];
        }
        $newArr = [];
        if (empty($keys) || empty($record)) {
            return $newArr;
        }
        foreach ($record as $k => $v) {
            if (in_array($k, $keys)) {
                $newArr[$k] = $v;
            }
        }
        return $newArr;
    }

    
    public static function keepItemsKeys(&$records, $keys)
    {
        $newArr = [];
        if (empty($keys)) {
            return $newArr;
        }
        foreach ($records as $v) {
            $item = [];
            foreach ($v as $kk => $vv) {
                if (in_array($kk, $keys)) {
                    $item[$kk] = $vv;
                }
            }
            $newArr[] = $item;
        }
        return $newArr;
    }

    
    public static function removeKeys($record, $keys)
    {
        if (empty($keys) || empty($record)) {
            return $record;
        }
        $newArr = [];
        foreach ($record as $k => $v) {
            if (!in_array($k, $keys)) {
                $newArr[$k] = $v;
            }
        }
        return $newArr;
    }

    
    public static function renameItemsKey(&$records, $keyMap)
    {
        foreach ($records as $k => $v) {
            foreach ($keyMap as $old => $new) {
                $records[$k][$new] = $records[$k][$old];
                unset($records[$k][$old]);
            }
        }
    }

    
    public static function random($records)
    {
        if (empty($records)) {
            return null;
        }
        if (count($records) == 1) {
            return $records[0];
        }
        return $records[array_rand($records)];
    }


    
    public static function trimItems($records)
    {
        $newArr = [];
        foreach ($records as $k => $v) {
            if (is_array($v)) {
                $newArr[$k] = self::trims($v);
            } else {
                $newArr[$k] = trim($v);
            }
        }
        return $newArr;
    }

    
    public static function isAllEmpty($records)
    {
        if (empty($records) || !is_array($records)) {
            return true;
        }
        for ($i = 0; $i < count($records); $i++) {
            $v = trim($records[$i]);
            if (!empty($v)) {
                return false;
            }
        }
        return true;
    }

    
    public static function sortByKey($records, $key = 'sort', $sort = 'asc')
    {
        usort($records, function ($o1, $o2) use ($key, $sort) {
            if ($o1[$key] == $o2[$key]) {
                return 0;
            }
            $ret = $o1[$key] > $o2[$key] ? 1 : -1;
            return $sort == 'asc' ? $ret : -$ret;
        });
        return $records;
    }

    
    public static function keep(&$records, $size = 10)
    {
        $results = [];
        $count = 0;
        foreach ($records as $k => $v) {
            $results[$k] = $v;
            $count++;
            if ($count >= $size) {
                return $results;
            }
        }
        return $results;
    }

    
    public static function diff(array $olds, array $news)
    {
        $deletes = [];
        $inserts = [];
        foreach ($news as $o) {
            if (!in_array($o, $olds)) {
                $inserts[] = $o;
            }
        }
        foreach ($olds as $o) {
            if (!in_array($o, $news)) {
                $deletes[] = $o;
            }
        }
        return [$inserts, $deletes];
    }

    
    public static function diffWithKeys(array $old, array $new)
    {
        $deletes = [];
        $inserts = [];
        $changes = [];
        $oldKeys = array_keys($old);
        $newKeys = array_keys($new);
        foreach ($new as $k => $v) {
            if (in_array($k, $oldKeys)) {
                if ($v != $old[$k]) {
                    $changes[$k] = $v;
                }
            } else {
                $inserts[$k] = $v;
            }
        }
        foreach ($old as $k => $v) {
            if (!in_array($k, $newKeys)) {
                $deletes[$k] = $v;
            }
        }
        return [$inserts, $changes, $deletes];
    }
}
