<?php


namespace Module\Member\Util;


use Carbon\Carbon;
use ModStart\Core\Dao\ModelUtil;
use Module\Member\Model\MemberMeta;

class MemberMetaUtil
{
    public static function set($memberUserId, $name, $value = null)
    {
        $where = [
            'memberUserId' => $memberUserId,
            'name' => $name,
        ];
        if (is_null($value)) {
            ModelUtil::delete(MemberMeta::class, $where);
        } else {
            if (ModelUtil::update(MemberMeta::class, $where, [
                    'value' => $value,
                    'updated_at' => Carbon::now()
                ]) <= 0) {
                ModelUtil::transactionBegin();
                $one = ModelUtil::getWithLock(MemberMeta::class, $where);
                if (empty($one)) {
                    ModelUtil::insert(MemberMeta::class, array_merge($where, [
                        'value' => $value,
                    ]));
                }
                ModelUtil::transactionCommit();
            }
        }
    }

    public static function get($memberUserId, $name)
    {
        $where = [
            'memberUserId' => $memberUserId,
            'name' => $name,
        ];
        $meta = ModelUtil::get(MemberMeta::class, $where);
        if ($meta) {
            return $meta['value'];
        }
        return null;
    }
}
