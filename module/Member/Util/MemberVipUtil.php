<?php


namespace Module\Member\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleManager;
use Module\Vendor\Cache\CacheUtil;

class MemberVipUtil
{
    public static function isEnable()
    {
        return ModuleManager::getModuleConfigBoolean('Member', 'vipEnable');
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

    public static function getMemberVipById($memberUserId, $key = null, $defaultValue = null)
    {
        $memberUser = MemberUtil::get($memberUserId);
        return self::getMemberVip($memberUser, $key, $defaultValue);
    }

    public static function getMemberVip($memberUser, $key = null, $defaultValue = null)
    {
        if (empty($memberUser)) {
            $vip = self::get(null);
        } else if (!empty($memberUser['vipExpire']) && strtotime($memberUser['vipExpire']) > time()) {
            $vip = self::get($memberUser['vipId']);
        } else {
            $vip = self::get(null);
        }
        if (empty($vip)) {
            return null;
        }
        if (null === $key) {
            return $vip;
        }
        return isset($vip[$key]) ? $vip[$key] : $defaultValue;
    }

    public static function getDefaultVip()
    {
        foreach (self::map() as $vipId => $vip) {
            if (!empty($vip['isDefault'])) {
                return $vip;
            }
        }
        return null;
    }

    public static function get($vipId, $key = null)
    {
        $map = self::map();
        if (null === $key) {
            if (isset($map[$vipId])) {
                return $map[$vipId];
            }
            return self::getDefaultVip();
        } else {
            if (isset($map[$vipId][$key])) {
                return $map[$vipId][$key];
            }
            $vip = self::getDefaultVip();
            if (isset($vip[$key])) {
                return $vip[$key];
            }
        }
        return null;
    }

    /**
     * 会员卡升级差价计算
     *
     * @param $oldVipId
     * @param $newVipId
     * @return array
     */
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
                // 老会员折算
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
