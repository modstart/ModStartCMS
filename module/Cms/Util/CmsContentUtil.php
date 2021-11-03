<?php


namespace Module\Cms\Util;


use Carbon\Carbon;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\TagUtil;
use Module\Cms\Type\CmsModelContentStatus;

class CmsContentUtil
{
    public static function paginate($page, $pageSize, $option = [])
    {
        $option['where']['status'] = CmsModelContentStatus::SHOW;
        if (!isset($option['whereOperate'])) {
            $option['whereOperate'] = [];
        }
        $option['whereOperate'][] = ['postTime', '<', date('Y-m-d H:i:s')];
        $option['whereOrder'] = [
            ['isTop', 'desc'],
            ['postTime', 'desc']
        ];
        $paginateData = ModelUtil::paginate('cms_content', $page, $pageSize, $option);
        foreach ($paginateData['records'] as $k => $record) {
            if ($record['alias']) {
                $paginateData['records'][$k]['_url'] = modstart_web_url('a/' . $record['alias']);
            } else {
                $paginateData['records'][$k]['_url'] = modstart_web_url('a/' . $record['id']);
            }
            $paginateData['records'][$k]['_day'] = Carbon::parse($record['postTime'])->toDateString();
            $paginateData['records'][$k]['tags'] = TagUtil::string2Array($record['tags']);
        }
        return $paginateData;
    }

    public static function paginateCat($catId, $page, $pageSize, $option = [])
    {
        $catIds = CmsCatUtil::childrenIds($catId);
        $option['whereIn'] = [
            [
                'catId', $catIds,
            ]
        ];
        return self::paginate($page, $pageSize, $option);
    }

    public static function get($id)
    {
        $record = ModelUtil::get('cms_content', $id);
        BizException::throwsIfEmpty('记录不存在', $record);
        $model = CmsModelUtil::get($record['modelId']);
        $table = "cms_m_$model[name]";
        $recordData = ModelUtil::get($table, $record['id']);
        $record['_data'] = $recordData;
        return [
            'record' => $record,
            'model' => $model,
        ];
    }

    public static function getByAlias($alias)
    {
        $record = ModelUtil::get('cms_content', ['alias' => $alias]);
        BizException::throwsIfEmpty('记录不存在', $record);
        $model = CmsModelUtil::get($record['modelId']);
        $table = "cms_m_$model[name]";
        $recordData = ModelUtil::get($table, $record['id']);
        $record['_data'] = $recordData;
        return [
            'record' => $record,
            'model' => $model,
        ];
    }
}
