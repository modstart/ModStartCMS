<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;

class TagUtil
{
    public static function seperated2Array($tags, $seperator = ',')
    {
        if (',' == $seperator) {
            $tags = str_replace(['，'], ',', $tags);
        }
        return array_filter(explode(',', $tags));
    }

    public static function seperated2String($tags, $seperator = ',')
    {
        $results = self::seperated2Array($tags, $seperator);
        return self::array2String($results);
    }

    public static function array2String(array $tags)
    {
        $filterTags = [];
        foreach ($tags as &$tag) {
            $tag = trim($tag);
            if ($tag == '' || Str::contains($tag, ':')) {
                continue;
            }
            $filterTags[] = ':' . $tag . ':';
        }
        return join('', array_unique($filterTags));
    }

    /**
     * @param string $tags
     * @param string $format 格式，auto自动识别 string字符串
     * @return array
     */
    public static function string2Array($tags, $format = 'auto')
    {
        if (is_array($tags)) {
            $tags = join('::', $tags);
            if (!empty($tags)) {
                $tags = ":$tags:";
            }
        }
        $tags = trim($tags, ':');
        $tags = explode('::', $tags);
        $filterTags = [];
        foreach ($tags as &$tag) {
            $tag = trim($tag);
            if (empty($tag)) {
                continue;
            }
            if ($format == 'auto' && is_numeric($tag)) {
                $filterTags[] = intval($tag);
            } else {
                $filterTags[] = $tag;
            }
        }
        return array_unique($filterTags);
    }

    public static function recordsString2Array(&$records, $keyArray, $format = 'auto')
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $record[$key] = self::string2Array($record[$key], $format);
            }
        }
    }

    public static function recordsArray2String(&$records, $keyArray)
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $record[$key] = self::array2String($record[$key]);
            }
        }
    }

    public static function mapInfo($tags, array $tagMap = [])
    {
        foreach ($tags as &$tag) {
            if (array_key_exists($tag, $tagMap)) {
                $tag = $tagMap[$tag];
            }
        }
        return $tags;
    }

    public static function map($tags, array $tagMap = [])
    {
        if (is_string($tags)) {
            $tags = self::string2Array($tags);
        }
        $mapped = [];
        foreach ($tags as $tag) {
            if (array_key_exists($tag, $tagMap)) {
                $mapped[$tag] = $tagMap[$tag];
            } else {
                $mapped[$tag] = null;
            }
        }
        return $mapped;
    }

    public static function urlJoin($url, array $tags, $except = null, $tagType = 'number', $glue = '_')
    {
        if (null !== $except) {
            foreach ($tags as $index => $tag) {
                if ($tag == $except) {
                    unset($tags[$index]);
                }
            }
        }
        if ('number' == $tagType) {
            sort($tags, SORT_NUMERIC);
        } else {
            sort($tags, SORT_STRING);
        }
        if ($url) {
            $tags = array_merge([$url], $tags);
        }
        return join($glue, $tags);
    }
}
