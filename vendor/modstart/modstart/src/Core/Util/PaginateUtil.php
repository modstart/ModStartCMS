<?php


namespace ModStart\Core\Util;


/**
 * @Util 分页工具
 */
class PaginateUtil
{
    /**
     * @Util 对数组数据进行内存分页
     * @param $records array 完整数据集
     * @param $page int 当前页码（从 1 开始）
     * @param $pageSize int 每页条数
     * @return array [page, pageSize, total, records]
     */
    public static function pack($records, $page, $pageSize)
    {
        $ret = [];
        $ret['page'] = $page;
        $ret['pageSize'] = $pageSize;
        $ret['total'] = count($records);
        $start = ($pageSize * ($page - 1));
        if ($start < 0 || $start >= count($records)) {
            $ret['records'] = [];
        } else {
            $ret['records'] = array_slice($records, $start, $pageSize);
        }
        return $ret;
    }

    /**
     * @Util 将数组数据封装为单页分页结果格式
     * @param $records array 数据集
     * @return array [page=1, pageSize=count, total, records]
     */
    public static function pack1($records)
    {
        $ret = [];
        $ret['page'] = 1;
        $ret['pageSize'] = count($records);
        $ret['total'] = count($records);
        $ret['records'] = $records;
        return $ret;
    }
}
