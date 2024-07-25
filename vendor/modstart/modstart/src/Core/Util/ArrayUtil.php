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
        return SerializeUtil::jsonEncode($arr1) == SerializeUtil::jsonEncode($arr2);
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
     * 随机获取一个元素
     * @param $records array 二维数组
     * @return mixed|null
     */
    public static function random($records)
    {
        if (empty($records)) {
            return null;
        }
        return $records[array_rand($records)];
    }

    /**
     * 根据优先级随机获取一个元素
     * @param $records array 二维数组
     * @param $priorityKey string 优先级字段
     * @return mixed|null
     */
    public static function randomWithPriority($records, $priorityKey = 'priority')
    {
        if (empty($records)) {
            return null;
        }
        $recordKeys = [];
        foreach ($records as $k => $v) {
            $priority = min($v[$priorityKey], 100);
            for ($i = 0; $i < $priority; $i++) {
                $recordKeys[] = $k;
            }
        }
        if (empty($recordKeys)) {
            return null;
        }
        $recordKey = self::random($recordKeys);
        return $records[$recordKey];
    }

    /**
     * 随机获取N个元素
     * @param $records array 二维数组
     * @param $n int 获取数量
     * @return array|null
     */
    public static function randomN($records, $n)
    {
        if (empty($records)) {
            return null;
        }
        if (count($records) <= $n) {
            return $records;
        }
        $keys = array_rand($records, $n);
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $result = [];
        foreach ($keys as $key) {
            $result[] = $records[$key];
        }
        return $result;
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

    /**
     * 根据多个key排序
     *
     * @param $records array
     * @param $keys array
     * @return array
     * @example
     * $keyOrder = [
     * ['key' => 'type', 'sort' => 'asc', 'valueOrder' => ['dir']],
     * ['key' => 'name', 'sort' => 'asc'],
     * ]
     */
    public static function sortByKeys($records, $keysOrder = [])
    {
        usort($records, function ($o1, $o2) use ($keysOrder) {
            foreach ($keysOrder as $order) {
                $orderKey = $order['key'];
                $orderSort = $order['sort'];
                if ($o1[$orderKey] == $o2[$orderKey]) {
                    continue;
                }
                $orderValueOrder = isset($order['valueOrder']) ? $order['valueOrder'] : null;
                $v1 = $o1[$orderKey];
                $v2 = $o2[$orderKey];
                if (!is_null($orderValueOrder)) {
                    $v1 = array_search($v1, $orderValueOrder);
                    $v2 = array_search($v2, $orderValueOrder);
                    if ($v1 === false && $v2 === false) {
                        $v1 = $o1[$orderKey];
                        $v2 = $o2[$orderKey];
                    } else if ($v1 === false) {
                        $v1 = $v2 + 1;
                    } else {
                        $v2 = $v1 + 1;
                    }
                }
                $ret = $v1 > $v2 ? 1 : -1;
                return $orderSort == 'asc' ? $ret : -$ret;
            }
            return 0;
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
        return SerializeUtil::jsonEncode($params);
    }

    public static function update(&$original, $update)
    {
        foreach ($update as $k => $v) {
            $original[$k] = $v;
        }
    }

    public static function getByDotKey($array, $key, $defaultValue = null)
    {
        if (empty($key)) {
            return $defaultValue;
        }
        if (empty($array)) {
            return $defaultValue;
        }
        if (strpos($key, '.') === false) {
            return isset($array[$key]) ? $array[$key] : $defaultValue;
        }
        $keys = explode('.', $key);
        $value = $array;
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $defaultValue;
            }
            $value = $value[$k];
        }
        return $value;
    }

    public static function updateByDotKey(&$array, $key, $value)
    {
        if (strpos($key, '.') === false) {
            $array[$key] = $value;
            return;
        }
        $keys = explode('.', $key);
        $lastKey = array_pop($keys);
        $value = &$array;
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                $value[$k] = [];
            }
            $value = &$value[$k];
        }
        $value[$lastKey] = $value;
    }

    public static function shuffleAssoc($list)
    {
        if (!is_array($list)) {
            return $list;
        }
        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }
        return $random;
    }

    /**
     * @Util 获取数据中的第一个非空值
     * @param $default mixed 默认值
     * @param ...$value mixed 可选值
     * @return mixed
     */
    public static function firstValidValue($default, ...$value)
    {
        foreach ($value as $v) {
            $v = ValueUtil::value($v);
            if (!empty($v)) {
                return $v;
            }
        }
        return $default;
    }

}
