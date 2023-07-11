<?php


namespace ModStart\Core\Util;

class XKeywordsUtil
{
    /**
     * 规则匹配
     * @param $keywords string 关键词，使用逗号或换行符分割
     * @param $content string 需要匹配的内容
     * @param $matKeyword string 匹配到的关键词
     */
    public static function match($keywords, $content, &$matKeyword = null)
    {
        $keywords = trim($keywords);
        if (empty($keywords) || empty($content)) {
            return false;
        }
        $list = str_replace(',', "\n", $keywords);
        $list = explode("\n", $list);
        foreach ($list as $item) {
            $item = trim($item);
            if (empty($item)) {
                continue;
            }
            if (false !== strpos($content, $item)) {
                $matKeyword = $item;
                return true;
            } else if (substr($item, 0, 1) === '@') {
                if (@preg_match('/' . substr($item, 1) . '/', $content)) {
                    $matKeyword = $item;
                    return true;
                }
            } else if (false !== strpos($item, ' ')) {
                $allMatch = true;
                foreach (explode(' ', $item) as $k) {
                    if (false === strpos($content, $k)) {
                        $allMatch = false;
                        break;
                    }
                }
                if ($allMatch) {
                    $matKeyword = $item;
                    return true;
                }
            } else if (preg_match('/((\\*)(\\d+))[^\\d]/', $item, $mat)) {
                $ks = explode($mat[1], $item);
                $regx = '/' . preg_quote($ks[0]) . '.{3,' . $mat[3] * 3 . '}' . preg_quote($ks[1]) . '/';
                if (@preg_match($regx, $content)) {
                    $matKeyword = $item;
                    return true;
                }
            } else if (false !== strpos($item, '*')) {
                $ks = explode('*', $item);
                $regx = '/' . preg_quote($ks[0]) . '.+' . preg_quote($ks[1]) . '/';
                if (@preg_match($regx, $content)) {
                    $matKeyword = $item;
                    return true;
                }
            }
        }
        return false;
    }

    public static function descriptionHtml()
    {
        return "<pre class='ub-content-bg'>" . self::description() . "</pre>";
    }

    public static function description()
    {
        $text = <<< TEXT
关键词匹配说明
· 简单包含匹配: "你好" 匹配 "你好"多条使用,分割
· 忽略顺序同时包含匹配: "你 好" 匹配 "你们好"、"你们好"、"很好你们"
· 顺序同时包含匹配: "你*好" 匹配 "你们好"、"你们很好"，*匹配一个或多个字
· 顺序限定同时包含匹配: "你*1好" 匹配 "你们好"，不能匹配"你们很好"，数字表示最长多少个汉字字符，1个汉字=3英文字母
· 正则表达式匹配："@正则表达式" 匹配 "正则表达式"，@开头表示正则表达式
使用说明
· 多个正则使用半角逗号(,)或换行分割
TEXT;

        return $text;
    }


    public static function test()
    {
        $tests = [
            '你好', '你好',
            '你好', '你们好',
            '你 好', '你们好',
            '你*好', '你们好',
            '你*1好', '你们好',
            '你*1好', '你们很好',
            '你*2好', '你们很好',
            '@你们', '你们很好',
            '@你好', '你们很好',
        ];
        $results = [];
        for ($i = 0; $i < count($tests); $i += 2) {
            $results[] = sprintf('%-10s%-20s%s', $tests[$i], $tests[$i + 1], json_encode(self::match($tests[$i], $tests[$i + 1])));
        }
        return join("\n", $results);
    }
}
