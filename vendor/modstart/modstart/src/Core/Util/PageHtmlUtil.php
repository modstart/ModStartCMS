<?php

namespace ModStart\Core\Util;

/**
 * @Util 分页渲染工具
 */
class PageHtmlUtil
{
    private static function itemRender($start, $end, $currentPage, $url, $template)
    {
        $html = [];
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html[] = self::replace($template['current'], [
                    '%p%' => $i
                ]);
            } else {
                $html[] = self::replace($template['item'], [
                    '%p%' => $i,
                    '%s%' => self::buildPage($url, $i)
                ]);
            }
        }
        return join('', $html);
    }

    /**
     * @Util 渲染下一页分页链接
     * @param $total int 总记录数
     * @param $pageSize int 每页记录数
     * @param $currentPage int 当前页
     * @param $url string 分页链接，页码使用 {page} 占位
     */
    public static function nextPageUrl($total, $pageSize, $currentPage, $url = '/url/for/path?page={page}')
    {
        $totalPage = ceil($total / $pageSize);
        if ($currentPage < $totalPage) {
            return self::buildPage($url, $currentPage + 1);
        }
        return null;
    }

    /**
     * @Util 渲染上一页分页链接
     * @param $total int 总记录数
     * @param $pageSize int 每页记录数
     * @param $currentPage int 当前页
     * @param $url string 分页链接，页码使用 {page} 占位
     */
    public static function prevPageUrl($total, $pageSize, $currentPage, $url = '/url/for/path?page={page}')
    {
        if ($currentPage > 1) {
            return self::buildPage($url, $currentPage - 1);
        }
        return null;
    }

    private static function replace($tpl, $param = [])
    {
        return str_replace(array_keys($param), array_values($param), $tpl);
    }

    private static function buildPage($url, $page)
    {
        return str_replace('{page}', $page, $url);
    }

    /**
     * @Util 渲染分页工具
     * @param $total int 总记录数
     * @param $pageSize int 每页记录数
     * @param $currentPage int 当前页
     * @param $url string 分页链接，页码使用 {page} 占位
     * @param $template string 模板
     */
    public static function render($total, $pageSize, $currentPage, $url = '/url/for/path?page={page}', $template = null)
    {
        if (is_null($template)) {
            $template = [
                'warp' => '<div class="pages">%s%</div>',
                'more' => '<span class="more">...</span>',
                'prev' => '<a class="page" href="%s%">' . L('PrevPage') . '</a>',
                'prevDisabled' => null,
                'next' => '<a class="page" href="%s%">' . L('NextPage') . '</a>',
                'nextDisabled' => null,
                'current' => '<span class="current">%p%</span>',
                'item' => '<a class="page" href="%s%">%p%</a>',
            ];
        }

        $totalPage = ceil($total / $pageSize);

        if ($currentPage < 1) {
            $currentPage = 1;
        } else if ($currentPage > $totalPage) {
            $currentPage = $totalPage;
        }

        $html = [];

        if (!empty($template['first'])) {
            $html[] = self::replace($template['first'], [
                '%s%' => self::buildPage($url, 1),
            ]);
        }

        if ($currentPage > 1) {
            $html[] = self::replace($template['prev'], [
                '%s%' => self::buildPage($url, $currentPage - 1),
                '%p%' => $currentPage - 1,
            ]);
        } else {
            if (!empty($template['prevDisabled'])) {
                $html[] = $template['prevDisabled'];
            }
        }

        if ($totalPage < 6) {
            if ($totalPage > 0) {
                $html[] = self::itemRender(1, $totalPage, $currentPage, $url, $template);
            }
        } else {

            $html[] = self::itemRender(1, 3, $currentPage, $url, $template);

            $midStart = $currentPage - 3;
            $midEnd = $currentPage + 3;
            if ($midStart < 4) {
                $midStart = 4;
            }
            if ($midEnd > $totalPage - 3) {
                $midEnd = $totalPage - 3;
            }
            if ($midStart > 3 + 1) {
                $html[] = $template['more'];
            }

            $html[] = self::itemRender($midStart, $midEnd, $currentPage, $url, $template);

            if ($midEnd < $totalPage - 3) {
                $html[] = $template['more'];
            }

            $html[] = self::itemRender($totalPage - 2, $totalPage, $currentPage, $url, $template);
        }

        if ($currentPage < $totalPage) {
            $html[] = self::replace($template['next'], [
                '%s%' => self::buildPage($url, $currentPage + 1),
            ]);
        } else {
            if (!empty($template['nextDisabled'])) {
                $html[] = $template['nextDisabled'];
            }
        }

        if (!empty($template['last'])) {
            $html[] = self::replace($template['last'], [
                '%s%' => self::buildPage($url, $totalPage),
            ]);
        }

        return self::replace($template['warp'], [
            '%s%' => join('', $html),
        ]);
    }
}
