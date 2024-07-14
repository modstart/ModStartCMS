<?php

namespace ModStart\Core\Util;

class ParamUtil
{

    public static function getPlaceholdersHtml(array $param, $option = [])
    {
        $placeholders = self::getPlaceholders($param, $option);
        $html = [];
        $html[] = '<table class="ub-table mini ub-content-bg">';;
        foreach ($placeholders as $key => $value) {
            $html[] = '<tr><td width="200" data-clipboard-text="' . htmlspecialchars($key) . '" class="tw-cursor-pointer">' . $key . '</td><td>' . $value . '</td></tr>';
        }
        $html[] = '</table>';
        return '<code>' . join(' ', $html) . '</code>';
    }

    public static function getPlaceholders(array $param, $option = [])
    {
        $option = array_merge([
            'prefix' => '${',
            'suffix' => '}',
            'placeholders' => ['Timestamp', 'TimestampInMS'],
            'functions' => ['md5'],
        ], $option);
        $placeholders = [];
        foreach ($param as $k => $v) {
            $placeholders[$option['prefix'] . $k . $option['suffix']] = $v;
        }
        if (in_array('Timestamp', $option['placeholders'])) {
            $placeholders[$option['prefix'] . 'Timestamp' . $option['suffix']] = '当前时间戳，单位秒';
        }
        if (in_array('TimestampInMS', $option['placeholders'])) {
            $placeholders[$option['prefix'] . 'TimestampInMS' . $option['suffix']] = '当前时间戳，单位毫秒';
        }
        if (in_array('md5', $option['functions'])) {
            $exampleCallText = $option['prefix'] . 'md5(\'' . $option['prefix'] . 'Param1' . $option['suffix'] . '' . $option['prefix'] . 'Param2' . $option['suffix'] . '\')' . $option['suffix'];
            $placeholders[$option['prefix'] . 'md5(\'Param\')' . $option['suffix']] = '对当前内容进行 md5 计算，如：' . $exampleCallText;
        }
        return $placeholders;
    }

    public static function replaceJsonPlaceholder($json, array $param, $option = [])
    {
        $text = SerializeUtil::jsonEncode($json);
        $text = self::replacePlaceholder($text, $param, $option);
        return SerializeUtil::jsonDecode($text);
    }

    public static function replacePlaceholder($text, array $param, $option = [])
    {
        $option = array_merge([
            'prefix' => '${',
            'suffix' => '}',
            'placeholders' => ['Timestamp', 'TimestampInMS'],
            'functions' => ['md5'],
        ], $option);
        if (in_array('Timestamp', $option['placeholders'])) {
            $param['Timestamp'] = time();
        }
        if (in_array('TimestampInMS', $option['placeholders'])) {
            $param['TimestampInMS'] = round(microtime(true) * 1000);
        }
        $search = array_map(function ($key) use ($option) {
            return $option['prefix'] . $key . $option['suffix'];
        }, array_keys($param));
        $replace = array_values($param);
        $text = str_replace($search, $replace, $text);
        $prefixRegex = preg_quote($option['prefix'], '/');
        $suffixRegex = preg_quote($option['suffix'], '/');
        if (in_array('md5', $option['functions'])) {
            $text = preg_replace_callback('/' . $prefixRegex . 'md5\(\'([^\']+)\'\)' . $suffixRegex . '/', function ($matches) {
                return md5($matches[1]);
            }, $text);
        }
        return $text;
    }
}
