<?php


namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use Module\Vendor\Cache\CacheUtil;

class MemberVipUtil
{
    public static function isEnable()
    {
        return modstart_config('moduleMemberVipEnable', false);
    }

    public static function all()
    {
        return CacheUtil::rememberForever('MemberVipList', function () {
            return ModelUtil::all('member_vip_set', [], ['*'], ['sort', 'asc']);
        });
    }

    public static function update($id, $data)
    {
        ModelUtil::update('member_vip_set', $id, $data);
        self::clearCache();
    }

    public static function map()
    {
        return CacheUtil::rememberForever('MemberVipMap', function () {
            $map = [];
            foreach (ModelUtil::all('member_vip_set', [], ['*'], ['sort', 'asc']) as $item) {
                $map[intval($item['id'])] = $item;
                if (!empty($item['isDefault'])) {
                    $map[0] = $item;
                }
            }
            return $map;
        });
    }

    public static function mapTitle()
    {
        return array_build(self::map(), function ($k, $v) {
            return [$k, $v['title']];
        });
    }

    public static function getMemberVip($memberUser)
    {
        if (empty($memberUser)) {
            return self::get(null);
        }
        if (!empty($memberUser['vipExpire']) && strtotime($memberUser['vipExpire']) > time()) {
            return self::get($memberUser['vipId']);
        }
        return self::get(null);
    }

    public static function get($vipId, $key = null)
    {
        $map = self::map();
        if (null === $key) {
            if (isset($map[$vipId])) {
                return $map[$vipId];
            }
            if (isset($map[0])) {
                return $map[0];
            }
        } else {
            if (isset($map[$vipId][$key])) {
                return $map[$vipId][$key];
            }
            if (isset($map[0][$key])) {
                return $map[0][$key];
            }
        }
        return null;
    }

    
    public static function calcPrice($oldVipId, $oldVipExpire, $newVipId)
    {
        $oldVip = self::get($oldVipId);
        $oldPrice = ($oldVip ? $oldVip['price'] : 0);
        $oldDays = ($oldVip ? $oldVip['vipDays'] : 0);

        $newVip = self::get($newVipId);
        $newPrice = ($newVip ? $newVip['price'] : 0);
        $newDays = ($newVip ? $newVip['vipDays'] : 0);

        if (empty($newVip)) {
            return Response::generate(-1, '数据错误');
        }

        $oldVipExpireTimestamp = (strtotime($oldVipExpire) > 0 ? strtotime($oldVipExpire) : 0);

        $expire = null;
        $price = 0;
        $type = null;

        if ($oldVipId == $newVipId) {
            $type = '续费会员';
            $expire = date('Y-m-d', max($oldVipExpireTimestamp, time()) + $newVip['vipDays'] * 24 * 3600);
            $price = $newVip['price'];
        } else {
            if ($oldVipExpireTimestamp > 0) {
                $type = '变更会员';
                $expireTimestamp = max(time() + $newVip['vipDays'] * 24 * 3600, $oldVipExpireTimestamp);
                $expire = date('Y-m-d', $expireTimestamp);
                $price = $newVip['price'] * (($expireTimestamp - time()) / (24 * 3600)) / $newVip['vipDays'];
                                $oldLeftDays = max(0, intval(($oldVipExpireTimestamp - time()) / (24 * 3600)));
                $oldTotalDays = $oldVip['vipDays'];
                $oldPriceValue = ($oldTotalDays > 0 ? ($oldVip['price'] * $oldLeftDays / $oldTotalDays) : 0);
                $price = max(bcsub($price, $oldPriceValue, 2), 0.01);
            } else {
                $type = '新开会员';
                $expire = date('Y-m-d', time() + $newVip['vipDays'] * 24 * 3600);
                $price = $newVip['price'];
            }
        }
        return Response::generate(0, 'ok', [
            'type' => $type,
            'expire' => $expire,
            'price' => $price,
        ]);

    }

    public static function clearCache()
    {
        CacheUtil::forget('MemberVipList');
        CacheUtil::forget('MemberVipMap');
    }
}