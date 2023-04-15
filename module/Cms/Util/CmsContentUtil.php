<?php


namespace Module\Cms\Util;


use Carbon\Carbon;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\TagUtil;
use Module\Cms\Field\CmsField;
use Module\Cms\Type\CmsContentVerifyStatus;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Type\ContentUrlMode;

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
        $option['where']['verifyStatus'] = CmsContentVerifyStatus::VERIFY_PASS;
        if (!isset($option['whereOperate'])) {
            $option['whereOperate'] = [];
        }
        $option['whereOperate'][] = ['postTime', '<', date('Y-m-d H:i:s')];
        if (empty($option['order'])) {
            $option['order'] = [
                ['isTop', 'desc'],
                ['isRecommend', 'desc'],
                ['postTime', 'desc'],
            ];
        }
        $paginateData = ModelUtil::paginate('cms_content', $page, $pageSize, $option);
        foreach ($paginateData['records'] as $k => $record) {
            if (!empty($record['cover'])) {
                $paginateData['records'][$k]['cover'] = AssetsUtil::fixFull($record['cover']);
            }
            $paginateData['records'][$k]['_url'] = ContentUrlMode::url($record);
            $paginateData['records'][$k]['_day'] = Carbon::parse($record['postTime'])->toDateString();
            $paginateData['records'][$k]['_tags'] = TagUtil::string2Array($record['tags']);
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
                'verifyStatus' => CmsContentVerifyStatus::VERIFY_PASS,
            ])
            ->where('postTime', '<', date('Y-m-d H:i:s'))
            ->orderBy('isTop', 'desc')
            ->orderBy('postTime', 'desc')
            ->get()->toArray();
        foreach ($records as $k => $record) {
            if (!empty($record['cover'])) {
                $records[$k]['cover'] = AssetsUtil::fixFull($record['cover']);
            }
            $records[$k]['_url'] = ContentUrlMode::url($record);
            $records[$k]['_day'] = Carbon::parse($record['postTime'])->toDateString();
            $records[$k]['_tags'] = TagUtil::string2Array($record['tags']);
            $model = CmsModelUtil::get($record['modelId']);
            $records[$k]['_data'] = CmsContentUtil::getModelData($model, $record['id']);
        }
        return $records;
    }

    public static function paginateCatsWithData($cats, $page, $pageSize, $option = [])
    {
        $catIds = array_map(function ($o) {
            return $o['id'];
        }, $cats);
        $option['whereIn'] = [
            [
                'catId', $catIds,
            ]
        ];
        $option['where']['status'] = CmsModelContentStatus::SHOW;
        $option['where']['verifyStatus'] = CmsContentVerifyStatus::VERIFY_PASS;
        if (!isset($option['whereOperate'])) {
            $option['whereOperate'] = [];
        }
        $option['whereOperate'][] = ['postTime', '<', date('Y-m-d H:i:s')];
        if (empty($option['order'])) {
            $option['order'] = [
                ['isTop', 'desc'],
                ['isRecommend', 'desc'],
                ['postTime', 'desc'],
            ];
        }
        $dataFields = [
            'content' => 'content',
        ];
        $dataModel = null;
        foreach ($cats as $cat) {
            if (empty($dataModel)) {
                $dataModel = $cat['_model'];
            } else {
                if ($cat['modelId'] != $dataModel['id']) {
                    BizException::throws('只能为同一模型');
                }
            }
        }
        BizException::throwsIfEmpty('模型为空', $dataModel);
        foreach ($dataModel['_customFields'] as $f) {
            $dataFields[$f['name']] = $f['name'];
        }
        $option['joins'] = [
            [
                'table' => ['cms_m_news', 'cms_content.id', '=', 'cms_m_news.id',],
                'fields' => $dataFields,
            ]
        ];
        $paginateData = ModelUtil::paginate('cms_content', $page, $pageSize, $option);
        foreach ($paginateData['records'] as $k => $record) {
            if (!empty($record['cover'])) {
                $paginateData['records'][$k]['cover'] = AssetsUtil::fixFull($record['cover']);
            }
            $paginateData['records'][$k]['_url'] = ContentUrlMode::url($record);
            $paginateData['records'][$k]['_day'] = Carbon::parse($record['postTime'])->toDateString();
            $paginateData['records'][$k]['_tags'] = TagUtil::string2Array($record['tags']);
            $paginateData['records'][$k] = CmsModelUtil::decodeCustomField($dataModel, $paginateData['records'][$k]);
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
        $record['_tags'] = TagUtil::string2Array($record['tags']);
        foreach ($model['_customFields'] as $v) {
            $f = CmsField::getByName($v['fieldType']);
            $recordData[$v['name']] = $f->unserializeValue($recordData[$v['name']], $recordData);
        }
        $record['_data'] = $recordData;
        if (!empty($record['cover'])) {
            $record['cover'] = AssetsUtil::fixFull($record['cover']);
        }
        return [
            'record' => $record,
            'model' => $model,
        ];
    }

    public static function getData($cat, $id)
    {
        if (empty($cat) || empty($id)) {
            return null;
        }
        $model = CmsModelUtil::get($cat['modelId']);
        $table = "cms_m_$model[name]";
        return ModelUtil::get($table, $id);
    }

    public static function increaseView($id)
    {
        ModelUtil::increase('cms_content', $id, 'viewCount');
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
        if (!empty($record['cover'])) {
            $record['cover'] = AssetsUtil::fixFull($record['cover']);
        }
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

    public static function delete($id)
    {

    }
}
