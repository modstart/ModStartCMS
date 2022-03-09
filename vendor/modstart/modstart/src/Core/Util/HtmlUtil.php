<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Misc\Html\Purifier;

class HtmlUtil
{
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

    public static function extractTextAndImages($content)
    {
        $summary = [
            'text' => '',
            'images' => []
        ];

        $text = preg_replace('/<[^>]+>/', '', $content);
        $summary['text'] = $text;

        preg_match_all('/<img.*?src="(.*?)".*?>/i', $content, $mat);
        if (!empty($mat[1])) {
            $summary['images'] = $mat[1];
        }

        return $summary;
    }

    public static function cover($content)
    {
        preg_match_all('/<img.*?src="(.*?)".*?>/i', $content, $mat);
        if (!empty($mat[1][0])) {
            return $mat[1][0];
        }
        return null;
    }

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

    public static function filter($content)
    {
        if (empty($content)) {
            return $content;
        }
        return Purifier::cleanHtml($content, [
            'HTML.Allowed' => 'b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1,h2,h3,h4,h5,blockquote,pre[class],code',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
            'CSS.MaxImgLength' => null,
        ]);
    }

    public static function filter2($content)
    {
        if (empty($content)) {
            return $content;
        }
        $replaces = [
            'index' => 0,
            'search' => [],
            'replace' => [],
            'newReplace' => [],
        ];

        preg_match_all('%<audio.*?src="((http://|https://|//)?[a-zA-Z0-9\\./]+)">.*?</audio>%i', $content, $audioMat);
        foreach ($audioMat[0] as $i => $v) {
            $replaces['search'][] = $v;
            $replaces['replace'][] = '--custom-element--' . ($replaces['index']++) . '--';
            $src = $audioMat[1][$i];
            $replaces['newReplace'][] = "<audio src=\"$src\"></audio>";
        }

        $content = str_replace($replaces['search'], $replaces['replace'], $content);
        $content = Purifier::cleanHtml($content, [
            'HTML.Allowed' => join(',', [
                'b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1,h2,h3,h4,h5,blockquote,pre[class],code',
                'table[style|cellspacing|width],tbody[style],tr[style],td[style|rowspan|colspan|width|valign]',
                'iframe[src]',
//                'section[style]',
            ]),
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => "%^(http://|https://|//)?([a-zA-Z0-9\\./]+)$%",
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
            'CSS.MaxImgLength' => null,
        ]);
        return str_replace($replaces['replace'], $replaces['newReplace'], $content);
    }

    /**
     * 将未格式化的文本进行HTML格式化
     *
     * @param string $text
     * @param boolean $htmlspecialchars
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
    public static function text2htmlSimpleRich($text, $htmlspecialchars = true)
    {
        $content = self::text2html($text, $htmlspecialchars);
        $content = preg_replace('|([\w\d]*)\s?(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', '<a href="$2" target="_blank">$0</a>', $content);
        return $content;
    }

    /**
     * 将使用text2html格式化的文本进行反HTML格式化
     *
     * @param string $text
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

