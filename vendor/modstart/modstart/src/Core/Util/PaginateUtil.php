<?php


namespace ModStart\Core\Util;


class PaginateUtil
{
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
