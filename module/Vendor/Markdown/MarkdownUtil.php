<?php

namespace Module\Vendor\Markdown;

use Illuminate\Support\Str;

class MarkdownUtil
{
    public static function convertToHtml($markdown)
    {
        if (PHP_VERSION_ID >= 80000) {
            return strval(Str::of($markdown)->markdown());
        }
        $converter = new MarkConverter([
            'renderer' => [
                'soft_break' => "<br />",
            ],
        ]);
        return $converter->convertToHtml($markdown);
    }

    public static function convertToMarkdown($html)
    {
        if (empty($html)) {
            return '';
        }

        $text = $html;

        // 代码块（需在行内代码之前处理）
        $text = preg_replace_callback('/<pre[^>]*>\s*<code[^>]*>(.*?)<\/code>\s*<\/pre>/is', function ($m) {
            $code = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $code = strip_tags($code);
            return "```\n" . $code . "\n```\n\n";
        }, $text);

        // 行内代码
        $text = preg_replace_callback('/<code[^>]*>(.*?)<\/code>/is', function ($m) {
            $code = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $code = strip_tags($code);
            return '`' . $code . '`';
        }, $text);

        // 标题
        foreach ([6, 5, 4, 3, 2, 1] as $level) {
            $prefix = str_repeat('#', $level);
            $text = preg_replace('/<h' . $level . '[^>]*>(.*?)<\/h' . $level . '>/is', $prefix . ' $1' . "\n\n", $text);
        }

        // 粗体
        $text = preg_replace('/<(strong|b)[^>]*>(.*?)<\/(strong|b)>/is', '**$2**', $text);

        // 斜体
        $text = preg_replace('/<(em|i)[^>]*>(.*?)<\/(em|i)>/is', '*$2*', $text);

        // 删除线
        $text = preg_replace('/<(del|s|strike)[^>]*>(.*?)<\/(del|s|strike)>/is', '~~$2~~', $text);

        // 链接（先匹配带 href 的 a 标签）
        $text = preg_replace_callback('/<a[^>]+href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/is', function ($m) {
            $href = $m[1];
            $label = strip_tags($m[2]);
            return '[' . $label . '](' . $href . ')';
        }, $text);

        // 图片（src 在前）
        $text = preg_replace('/<img[^>]+src=["\']([^"\']*)["\'][^>]*alt=["\']([^"\']*)["\'][^>]*\/?>/is', '![$2]($1)', $text);
        // 图片（alt 在前）
        $text = preg_replace('/<img[^>]+alt=["\']([^"\']*)["\'][^>]*src=["\']([^"\']*)["\'][^>]*\/?>/is', '![$1]($2)', $text);
        // 图片（无 alt）
        $text = preg_replace('/<img[^>]+src=["\']([^"\']*)["\'][^>]*\/?>/is', '![]($1)', $text);

        // 引用块
        $text = preg_replace_callback('/<blockquote[^>]*>(.*?)<\/blockquote>/is', function ($m) {
            $inner = trim(strip_tags($m[1]));
            $lines = explode("\n", $inner);
            return '> ' . implode("\n> ", $lines) . "\n\n";
        }, $text);

        // 无序列表
        $text = preg_replace_callback('/<ul[^>]*>(.*?)<\/ul>/is', function ($m) {
            $inner = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "- $1\n", $m[1]);
            return trim(strip_tags($inner, '')) . "\n\n";
        }, $text);

        // 有序列表
        $text = preg_replace_callback('/<ol[^>]*>(.*?)<\/ol>/is', function ($m) {
            $count = 0;
            $inner = preg_replace_callback('/<li[^>]*>(.*?)<\/li>/is', function ($n) use (&$count) {
                $count++;
                return $count . '. ' . $n[1] . "\n";
            }, $m[1]);
            return trim(strip_tags($inner, '')) . "\n\n";
        }, $text);

        // 段落
        $text = preg_replace('/<p[^>]*>(.*?)<\/p>/is', "$1\n\n", $text);

        // 换行
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);

        // 分割线
        $text = preg_replace('/<hr\s*\/?>/i', "\n---\n\n", $text);

        // 移除剩余标签
        $text = strip_tags($text);

        // 解码 HTML 实体
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 清理多余空行
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

}

