<?php


namespace Module\AigcBase\Util;


use Illuminate\Support\Facades\DB;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ArrayUtil;
use Module\AigcBase\Model\AigcKeyPool;
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

    private static function markEnd($idOrKey, $success)
    {
        $id = $idOrKey;
        if (is_array($id)) {
            $id = $idOrKey['id'];
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

    public static function markSuccess($key)
    {
        self::markEnd($key, true);
    }

    public static function markFail($key)
    {
        self::markEnd($key, false);
    }

}
