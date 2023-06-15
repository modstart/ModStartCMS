<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;

/**
 * 数据工具包
 */
class ArrayUtil
{
    /**
     * 判断两个数组是否相同（会先排序后比较）
     * @param array $arr1
     * @param array $arr2
     * @return bool
     */
    public static function sequenceEqual($arr1, $arr2)
    {
        sort($arr1);
        sort($arr2);
        return json_encode($arr1) == json_encode($arr2);
    }

    /**
     * 判断两个数组是否相同
     * @param array $arr1
     * @param array $arr2
     * @param null $keys 比较的键，如果为空比较所有键
     * @param bool $strict 是否使用严格模式 ===
     * @return bool
     */
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

    /**
     * 抽取Key对应的数据到新数组
     *
     * @param $records
     * @param $key
     * @return array
     */
    public static function flatItemsByKey(&$records, $key)
    {
        $result = [];
        foreach ($records as $item) {
            $result[] = $item[$key];
        }
        return $result;
    }

    /**
     * 过滤 $record 中的 $keys 并返回
     *
     * @param array $record
     * @param array $keys
     * @return array
     */
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

    /**
     * 只保留记录中的键
     * @param array( array() ) $records
     * @param array $keys
     * @return array
     */
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

    /**
     * 移除 $record 中的 $keys 并返回
     *
     * @param array $record
     * @param array $keys
     * @return array
     */
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

    public static function remove($array, $record)
    {
        return array_filter($array, function ($o) use ($record) {
            return $o != $record;
        });
    }

    public static function add($array, $record, $unique = true)
    {
        $array = array_merge($array, [$record]);
        if ($unique) {
            $array = array_unique($array);
        }
        return $array;
    }

    public static function removeAll($records, $recordsRemoved)
    {
        return array_values(array_filter($records, function ($o) use ($recordsRemoved) {
            return !in_array($o, $recordsRemoved);
        }));
    }

    public static function hasAny($records, $recordsCheck)
    {
        foreach ($recordsCheck as $item) {
            if (in_array($item, $records)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 重命名Keys
     *
     * @param array(array()) &$records
     * @param array $keyMap [old=>new]
     */
    public static function renameItemsKey(&$records, $keyMap)
    {
        foreach ($records as $k => $v) {
            foreach ($keyMap as $old => $new) {
                $records[$k][$new] = $records[$k][$old];
                unset($records[$k][$old]);
            }
        }
    }

    /**
     * @param $records
     * @return mixed|null
     */
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


    /**
     * trim所有数组中的元素
     *
     * @param $records
     * @return array
     */
    public static function trimItems($records)
    {
        $newArr = [];
        foreach ($records as $k => $v) {
            if (is_array($v)) {
                $newArr[$k] = self::trimItems($v);
            } else {
                $newArr[$k] = trim($v);
            }
        }
        return $newArr;
    }

    /**
     * 是否所有的都为空
     *
     * @param $records
     * @return bool
     */
    public static function isAllEmpty($records)
    {
        if (empty($records) || !is_array($records)) {
            return true;
        }
        foreach ($records as $v) {
            if (is_string($v)) {
                $v = trim($v);
            }
            if (!empty($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 根据key排序
     *
     * @param array $records
     * @param string $key
     * @param string $sort
     * @return mixed
     */
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

    public static function sortNumber($records)
    {
        sort($records, SORT_NUMERIC);
        return $records;
    }

    /**
     * 只保留特定数量
     *
     * @param $records
     * @param int $size
     * @return array
     */
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

    /**
     * 计算两个数组的差异
     * @param array $olds
     * @param array $news
     * @return array
     *
     * @example
     *
     * list($inserts, $deletes) = ArrayUtil::diff($olds,$news);
     */
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

    /**
     * 检测两个数组内容是否有变更
     *
     * @param array $old
     * @param array $new
     * @param array $keys
     * @return bool
     */
    public static function isChanged(array $old, array $new, array $keys)
    {
        foreach ($keys as $key) {
            if (!isset($old[$key]) && !isset($new[$key])) {
                continue;
            }
            if (!isset($old[$key])) {
                return true;
            }
            if (!isset($new[$key])) {
                return true;
            }
            if ($old[$key] != $new[$key]) {
                return true;
            }
        }
        return false;
    }

    /**
     * 根据键值对比两个数组的差异
     * @param array $old
     * @param array $new
     * @return array
     */
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


    private static function serializeForLogProcess($param, $sensitiveKeys)
    {
        if (is_string($param)) {
            return Str::limit($param, 100);
        } else if (is_array($param)) {
            foreach ($param as $k => $v) {
                if (in_array($k, $sensitiveKeys, true)) {
                    $param[$k] = '******';
                    continue;
                }
                $param[$k] = self::serializeForLogProcess($v, $sensitiveKeys);
            }
        }
        return $param;
    }

    public static function serializeForLog($params, $sensitiveKeys = ['password', 'passwordRepeat'])
    {
        if (!empty($params)) {
            if (is_array($params)) {
                foreach ($params as $i => $param) {
                    if (in_array($i, $sensitiveKeys, true)) {
                        $params[$i] = '******';
                        continue;
                    }
                    $params[$i] = self::serializeForLogProcess($param, $sensitiveKeys);
                }
            }
        }
        return json_encode($params, JSON_UNESCAPED_UNICODE);
    }

    public static function update(&$original, $update)
    {
        foreach ($update as $k => $v) {
            $original[$k] = $v;
        }
    }

}
