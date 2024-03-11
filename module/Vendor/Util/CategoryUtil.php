<?php

namespace Module\Vendor\Util;

use ModStart\Core\Dao\ModelUtil;

class CategoryUtil
{
    public static function importMultiLevel($model, $titles, $data = [], $option = [])
    {
        $option = array_merge([
            'separator' => '-',
            'sortField' => 'sort',
            'pidField' => 'pid',
            'idField' => 'id',
            'titleField' => 'title',
        ], $option);
        $titles = explode($option['separator'], $titles);
        $pid = 0;
        $category = null;
        while (count($titles) > 0) {
            $title = array_shift($titles);
            $where = [
                $option['pidField'] => $pid,
                $option['titleField'] => $title,
            ];
            $category = ModelUtil::get($model, $where);
            if (empty($category)) {
                $insert = array_merge($where, [
                    $option['sortField'] => ModelUtil::sortNext($model, [
                        $option['pidField'] => $pid,
                    ], $option['sortField']),
                ]);
                if (empty($titles)) {
                    $insert = array_merge($insert, $data);
                }
                $category = ModelUtil::insert($model, $insert);
            } else {
                if (empty($titles)) {
                    ModelUtil::update($model, [
                        $option['idField'] => $category[$option['idField']],
                    ], $data);
                    $category = ModelUtil::get($model, [
                        $option['idField'] => $category[$option['idField']],
                    ]);
                }
            }
            $pid = $category[$option['idField']];
        }
        return $category;
    }
}
