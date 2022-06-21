<?php

namespace ModStart\Core\Util;

/**
 * @Util 分页渲染工具
 */
class PageHtmlUtil
{
    private static function itemRender($start, $end, $currentPage, $url)
    {
        $html = [];
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html[] = '<span class="current">' . $i . '</span>';
            } else {
                $html[] = '<a class="page" href="' . str_replace('{page}', $i, $url) . '">' . $i . '</a>';
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
            return str_replace('{page}', ($currentPage + 1), $url);
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
            return str_replace('{page}', ($currentPage - 1), $url);
        }
        return null;
    }

    /**
     * @Util 渲染分页工具
     * @param $total int 总记录数
     * @param $pageSize int 每页记录数
     * @param $currentPage int 当前页
     * @param $url string 分页链接，页码使用 {page} 占位
     */
    public static function render($total, $pageSize, $currentPage, $url = '/url/for/path?page={page}')
    {
        $totalPage = ceil($total / $pageSize);

        if ($currentPage < 1) {
            $currentPage = 1;
        } else if ($currentPage > $totalPage) {
            $currentPage = $totalPage;
        }

        $html = [];


        $html[] = '<div class="pages">';

        if ($totalPage < 6) {
            if ($totalPage > 0) {
                $html[] = self::itemRender(1, $totalPage, $currentPage, $url);
            }
        } else {

            $html[] = self::itemRender(1, 3, $currentPage, $url);

            $midStart = $currentPage - 3;
            $midEnd = $currentPage + 3;
            if ($midStart < 4) {
                $midStart = 4;
            }
            if ($midEnd > $totalPage - 3) {
                $midEnd = $totalPage - 3;
            }
            if ($midStart > 3 + 1) {
                $html[] = '<span class="more">...</span>';
            }

            $html[] = self::itemRender($midStart, $midEnd, $currentPage, $url);

            if ($midEnd < $totalPage - 3) {
                $html[] = '<span class="more">...</span>';
            }

            $html[] = self::itemRender($totalPage - 2, $totalPage, $currentPage, $url);
        }

        if ($currentPage > 1) {
            $html[] = '<a class="page" href="' . str_replace('{page}', ($currentPage - 1), $url) . '">上一页</a>';
        }

        if ($currentPage < $totalPage) {
            $html[] = '<a class="page" href="' . str_replace('{page}', ($currentPage + 1), $url) . '">下一页</a>';
        }

        $html[] = '</div>';

        return join('', $html);
    }
}
