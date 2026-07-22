<?php


namespace Module\AigcBase\Util;


use Illuminate\Support\Facades\DB;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ArrayUtil;
use Module\AigcBase\Model\AigcKeyPool;
use Module\AigcBase\Provider\AigcChatProvider;
use Module\AigcBase\Provider\AigcProvider;
use Module\AigcBase\Type\AigcKeyPoolStatus;
use Module\Vendor\Util\CacheUtil;

class AigcKeyPoolUtil
{
    public static function clearCache()
    {
        CacheUtil::forget('AigcKeyPool');
    }

    public static function all()
    {
        return CacheUtil::rememberForever("AigcKeyPool", function () {
            $records = ModelUtil::all(AigcKeyPool::class, [
                'status' => AigcKeyPoolStatus::ONLINE,
            ], ['id', 'type', 'param', 'priority']);
            ModelUtil::decodeRecordsJson($records, ['param']);
            return $records;
        });
    }

    public static function allByType($type, $model)
    {
        $records = self::all();
        $records = array_filter($records, function ($o) use ($type, $model) {
            if ($o['type'] == $type) {
                if ($model == 'default') {
                    return true;
                } else {
                    if (isset($o['param']['model']) && $o['param']['model'] == $model) {
                        return true;
                    }
                }
            }
            return false;
        });
        return array_values($records);
    }

    public static function randomByType($type, $model)
    {
        $records = self::allByType($type, $model);
        return ArrayUtil::randomWithPriority($records);
    }

    private static function markEnd($idOrKeyPool, $success)
    {
        $id = $idOrKeyPool;
        if (is_array($id)) {
            $id = $idOrKeyPool['id'];
        }
        $update = [
            'callCount' => DB::raw('IFNULL(callCount,0)+1'),
            'lastCallTime' => date('Y-m-d H:i:s'),
        ];
        if ($success) {
            $update['successCount'] = DB::raw('IFNULL(successCount,0)+1');
        } else {
            $update['failCount'] = DB::raw('IFNULL(failCount,0)+1');
        }
        ModelUtil::update(AigcKeyPool::class, $id, $update);
    }

    public static function markSuccess($keyPool)
    {
        self::markEnd($keyPool, true);
    }

    public static function markFail($keyPool)
    {
        self::markEnd($keyPool, false);
    }

    public static function configuredChatModelMap()
    {
        $records = self::all();
        $result = [];
        foreach ($records as $record) {
            $model = !empty($record['param']['model']) ? $record['param']['model'] : 'default';
            $key = $record['type'] . ':' . $model;
            
            try {
                $provider = AigcProvider::getByName($record['type']);
                if ($provider) {
                    $models = $provider->models();
                    if (isset($models[$model])) {
                        // 使用 provider 的 models() 中的显示名
                        $result[$key] = $provider->title() . '-' . $models[$model];
                    } else {
                        // model 不在列表中（可能是动态输入的值），直接使用 model 名
                        $result[$key] = $model;
                    }
                }
            } catch (\Exception $e) {
                // 异常情况，使用 model 作为显示名
                $result[$key] = $model;
            }
        }
        return $result;
    }

}
