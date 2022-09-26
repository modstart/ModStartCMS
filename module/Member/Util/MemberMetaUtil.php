<?php


namespace Module\Member\Util;


use Carbon\Carbon;
use ModStart\Core\Dao\ModelUtil;

class MemberMetaUtil
{
    public static function set($memberUserId, $name, $value = null)
    {
        $where = [
            'memberUserId' => $memberUserId,
            'name' => $name,
        ];
        if (is_null($value)) {
            ModelUtil::delete('member_meta', $where);
        } else {
            if (ModelUtil::update('member_meta', $where, [
                    'value' => $value,
                    'updated_at' => Carbon::now()
                ]) <= 0) {
                ModelUtil::transactionBegin();
                $one = ModelUtil::getWithLock('member_meta', $where);
                if (empty($one)) {
                    ModelUtil::insert('member_meta', array_merge($where, [
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
        $meta = ModelUtil::get('member_meta', $where);
        if ($meta) {
            return $meta['value'];
        }
        return null;
    }
}
