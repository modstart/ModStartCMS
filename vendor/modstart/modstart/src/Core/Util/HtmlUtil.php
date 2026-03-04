<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Misc\Html\Purifier;


/**
 * @Util HTML 内容工具
 */
class HtmlUtil
{
    /**
     * @Util 将内容中的 img src 替换为懒加载属性
     * @param $content string HTML 内容
     * @param $dataAttr string 懒加载属性名，默认 data-src
     * @param $useAssets bool 是否使用 Assets CDN
     * @return string
     */
    public static function replaceImageSrcToLazyLoad($content, $dataAttr = 'data-src', $useAssets = false)
    {
        preg_match_all('/(<img.*?)src="(.*?)"(.*?>)/i', $content, $mat);
        if ($useAssets) {
            foreach ($mat[0] as $k => $v) {
                $content = str_replace($v, $mat[1][$k] . $dataAttr . '="' . AssetsUtil::fix($mat[2][$k]) . '"' . $mat[3][$k], $content);
            }
        } else {
            foreach ($mat[0] as $k => $v) {
                $content = str_replace($v, $mat[1][$k] . $dataAttr . '="' . AssetsUtil::fix($mat[2][$k]) . '"' . $mat[3][$k], $content);
            }
        }
        return $content;
    }

    /**
     * @Util 将内容中的 img src 替换为完整 URL
     * @param $content string HTML 内容
     * @param $useAssets bool 是否使用 Assets CDN
     * @param $useUrl string|null 自定义 URL 前缀
     * @return string
     */
    public static function replaceImageSrcToFull($content, $useAssets = false, $useUrl = null)
    {
        preg_match_all('/(<img.*?)src="(.*?)"(.*?>)/i', $content, $mat);
        foreach ($mat[0] as $k => $v) {
            if ($useUrl) {
                $content = str_replace($v, $mat[1][$k] . 'src="' . AssetsUtil::fixFullWithCdn($mat[2][$k], $useUrl) . '"' . $mat[3][$k], $content);
            } else if ($useAssets) {
                $content = str_replace($v, $mat[1][$k] . 'src="' . AssetsUtil::fixFull($mat[2][$k]) . '"' . $mat[3][$k], $content);
            } else {
                $content = str_replace($v, $mat[1][$k] . 'src="' . AssetsUtil::fixCurrentDomain($mat[2][$k]) . '"' . $mat[3][$k], $content);
            }
        }
        return $content;
    }

    /**
     * @Util 批量将记录集中指定字段的图片 src 替换为完整 URL
     * @param &$records array 记录集（引用传入）
     * @param $key string 字段名
     * @param $useAssets bool 是否使用 Assets CDN
     * @param $useUrl string|null 自定义 URL 前缀
     * @return void
     */
    public static function recordsReplaceImageSrcToFull(&$records, $key, $useAssets = false, $useUrl = null)
    {
        foreach ($records as $k => $v) {
            $records[$k][$key] = self::replaceImageSrcToFull($v[$key], $useAssets, $useUrl);
        }
    }

    /**
     * @Util 从 HTML 内容中提取文本和图片列表
     * @param $content string HTML 内容
     * @param $option array 选项（textBreakToSpace: 换行转空格）
     * @return array [text, images]
     */
    public static function extractTextAndImages($content, $option = [])
    {
        $option = array_merge([
            // 是否将换行转换为空格
            'textBreakToSpace' => false,
        ], $option);

        $summary = [
            'text' => '',
            'images' => []
        ];

        $text = preg_replace('/<[^>]+>/', '', $content);
        // 替换多余的空行
        $text = preg_replace('/\n\s*\n/', "\n", $text);
        if ($option['textBreakToSpace']) {
            $text = str_replace("\n", ' ', $text);
        }
        $summary['text'] = $text;

        preg_match_all('/<img.*?src="(.*?)".*?>/i', $content, $mat);
        if (!empty($mat[1])) {
            $summary['images'] = $mat[1];
        }

        return $summary;
    }

    /**
     * @Util 获取 HTML 内容中第一张图片 src
     * @param $content string HTML 内容
     * @return string|null
     */
    public static function cover($content)
    {
        preg_match_all('/<img.*?src="(.*?)".*?>/i', $content, $mat);
        if (!empty($mat[1][0])) {
            return $mat[1][0];
        }
        return null;
    }

    /**
     * @Util 提取 HTML 内容中的纯文本
     * @param $content string HTML 内容
     * @param $limit int|null 最大字符数限制
     * @return string
     */
    public static function text($content, $limit = null)
    {
        $text = preg_replace('/<[^>]+>/', '', $content);
        if (null !== $limit) {
            $text = Str::limit($text, $limit);
        }
        return str_replace([
            '&nbsp;',
        ], [
            ' ',
        ], $text);
    }

    /**
     * 富文本过滤
     * @param $content
     * @return mixed
     */
    public static function filter($content)
    {
        if (empty($content)) {
            return $content;
        }
        return Purifier::cleanHtml($content);
    }

    /**
     * @param $content
     * @return mixed
     * @deprecated
     */
    public static function filter2($content)
    {
        if (empty($content)) {
            return $content;
        }
        return Purifier::cleanHtml($content);
    }

    /**
     * 将未格式化的文本进行HTML格式化
     *
     * @param string $text
     * @param boolean $htmlspecialchars
     * @return string
     */
    /**
     * @Util 将纯文本转换为带段落多行的 HTML 内容
     * @param $text string 原始文本
     * @param $htmlspecialchars bool 是否对文本进行 HTML 转义
     * @return string
     */
    public static function text2html($text, $htmlspecialchars = true)
    {
        if (empty($text)) {
            return '';
        }
        if ($htmlspecialchars) {
            $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
        }
        $text = str_replace("\r", '', $text);
        $text = str_replace("\n", '</p><p>', $text);
        $text = str_replace('<p></p>', '<p>&nbsp;</p>', $text);
        return '<p>' . $text . '</p>';
    }

    /**
     * 将未格式化的文本进行HTML格式化，会自动解析网址、邮箱
     *
     * @param $text
     * @param bool $htmlspecialchars
     * @return string
     */
    /**
     * @Util 将纯文本转换为 HTML，并自动解析网址/邮箋为超链接
     * @param $text string 原始文本
     * @param $htmlspecialchars bool 是否对文本进行 HTML 转义
     * @return string
     */
    public static function text2htmlSimpleRich($text, $htmlspecialchars = true)
    {
        $content = self::text2html($text, $htmlspecialchars);
        return self::htmlSimpleRich($content);
    }

    /**
     * @param $html
     * @return array|string|string[]|null
     */
    /**
     * @Util 将 HTML 中的 http/https URL 自动转换为超链接（保留已有 HTML 标签）
     * @param $html string HTML 内容
     * @return string
     */
    public static function htmlSimpleRich($html)
    {
        $placeholders = [];
        $html = preg_replace_callback('/(<[^>]+?>)/i', function ($matches) use (&$placeholders) {
            $key = ' __HTML_TAG__|' . count($placeholders) . '|__ ';
            $placeholders[$key] = $matches[0];
            return $key;
        }, $html);
        $html = preg_replace_callback(
            '|(?<!["\'>])\b(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s<]*)|i',
            function ($matches) {
                $url = $matches[1];
                return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
            },
            $html
        );
        $html = str_replace(array_keys($placeholders), array_values($placeholders), $html);
        return $html;
    }

    /**
     * 将使用text2html格式化的文本进行反HTML格式化
     *
     * @param string $text
     * @return string
     */
    /**
     * @Util 将 text2html 格式化的 HTML 还原为纯文本
     * @param $text string HTML 内容
     * @return string
     */
    public static function html2text($text)
    {
        return str_replace(array(
            '</p>',
            '<p>'
        ), array(
            "\n",
            ''
        ), $text);
    }

    /**
     * @Util 统计 HTML 内容中的字数（中文字符 + 英文小词个数）
     * @param $content string HTML 内容
     * @return int
     */
    public static function workCount($content)
    {
        $content = preg_replace('/<[^>]+>/', '^', $content);
        // 统计英文
        preg_match_all('/[a-z0-9]+/i', $content, $mat);
        $englishCount = count($mat[0]);
        // 统计中文
        $content = str_replace('^', '', $content);
        $content = preg_replace('/[^\x{4e00}-\x{9fa5}]+/u', '', $content);
        $chineseCount = mb_strlen($content, 'utf-8');
        return $englishCount + $chineseCount;
    }

}

