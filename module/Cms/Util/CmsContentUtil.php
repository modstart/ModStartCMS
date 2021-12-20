<?php


namespace Module\Cms\Util;


use Carbon\Carbon;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\TagUtil;
use Module\Cms\Type\CmsModelContentStatus;

class CmsContentUtil
{
    public static function insert($model, $data, $dataData)
    {
        $data = ArrayUtil::keepKeys($data, [
            'catId', 'title', 'alias', 'summary', 'cover', 'postTime',
            'status', 'isRecommend', 'isTop', 'tags', 'author', 'source',
        ]);
        $data['modelId'] = $model['id'];
        $table = "cms_m_" . $model['name'];
        ModelUtil::transactionBegin();;
        $data = ModelUtil::insert('cms_content', $data);
        $dataData['id'] = $data['id'];
        ModelUtil::insert($table, $dataData);
        ModelUtil::transactionCommit();
        return $data['id'];
    }

    public static function paginate($page, $pageSize, $option = [])
    {
        $option['where']['status'] = CmsModelContentStatus::SHOW;
        if (!isset($option['whereOperate'])) {
            $option['whereOperate'] = [];
        }
        $option['whereOperate'][] = ['postTime', '<', date('Y-m-d H:i:s')];
        if (empty($option['order'])) {
            $option['order'] = [
                ['isTop', 'desc'],
                ['postTime', 'desc'],
            ];
        }
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

    public static function allCat($catId)
    {
        $catIds = CmsCatUtil::childrenIds($catId);
        if (empty($catIds)) {
            return [];
        }
        $records = ModelUtil::model('cms_content')
            ->whereIn('catId', $catIds)
            ->where([
                'status' => CmsModelContentStatus::SHOW,
            ])
            ->where('postTime', '<', date('Y-m-d H:i:s'))
            ->orderBy('isTop', 'desc')
            ->orderBy('postTime', 'desc')
            ->get()->toArray();
        foreach ($records as $k => $record) {
            if ($record['alias']) {
                $records[$k]['_url'] = modstart_web_url('a/' . $record['alias']);
            } else {
                $records[$k]['_url'] = modstart_web_url('a/' . $record['id']);
            }
            $records[$k]['_day'] = Carbon::parse($record['postTime'])->toDateString();
            $records[$k]['tags'] = TagUtil::string2Array($record['tags']);
            $model = CmsModelUtil::get($record['modelId']);
            $records[$k]['_data'] = CmsContentUtil::getModelData($model, $record['id']);
        }
        return $records;
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
        $record['_tags'] = TagUtil::string2Array($record['tags']);
        $record['_data'] = $recordData;
        return [
            'record' => $record,
            'model' => $model,
        ];
    }

    public static function getModelData($model, $id)
    {
        $table = "cms_m_$model[name]";
        $recordData = ModelUtil::get($table, $id);
        return $recordData;
    }

    public static function getByAlias($alias)
    {
        $record = ModelUtil::get('cms_content', ['alias' => $alias]);
        BizException::throwsIfEmpty('记录不存在', $record);
        $model = CmsModelUtil::get($record['modelId']);
        $table = "cms_m_$model[name]";
        $recordData = ModelUtil::get($table, $record['id']);
        $record['_tags'] = TagUtil::string2Array($record['tags']);
        $record['_data'] = $recordData;
        return [
            'record' => $record,
            'model' => $model,
        ];
    }

    public static function nextOne($catId, $dataId)
    {
        $option = [
            'order' => ['id', 'asc'],
            'whereOperate' => [
                ['id', '>', $dataId],
            ]
        ];
        $paginateData = CmsContentUtil::paginateCat($catId, 1, 1, $option);
        return isset($paginateData['records'][0]) ? $paginateData['records'][0] : null;
    }

    public static function prevOne($catId, $dataId)
    {
        $option = [
            'order' => ['id', 'desc'],
            'whereOperate' => [
                ['id', '<', $dataId],
            ]
        ];
        $paginateData = CmsContentUtil::paginateCat($catId, 1, 1, $option);
        return isset($paginateData['records'][0]) ? $paginateData['records'][0] : null;
    }
}
