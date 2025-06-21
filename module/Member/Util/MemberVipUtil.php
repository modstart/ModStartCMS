<?php


namespace Module\Member\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleManager;
use Module\Member\Auth\MemberUser;
use Module\Member\Events\MemberUserVipChangeEvent;
use Module\Member\Model\MemberVipSet;
use Module\Vendor\Util\CacheUtil;

class MemberVipUtil
{
    public static function isEnable()
    {
        return ModuleManager::getModuleConfig('Member', 'vipEnable', false);
    }

    public static function allWithGuest()
    {
        $vips = [];
        $vips[] = ['id' => 0, 'title' => '游客'];
        $vips = array_merge($vips, self::all());
        return $vips;
    }

    public static function allVisibleGroupNames()
    {
        $vips = self::allVisible();
        $groupNameMap = [];
        foreach ($vips as $v) {
            if ($v['isDefault']) {
                continue;
            }
            $groupNameMap[$v['groupName']] = $v;
        }
        return array_keys($groupNameMap);
    }

    public static function allVisible()
    {
        $vips = array_filter(self::all(), function ($vip) {
            return $vip['visible'];
        });
        $vips = array_values($vips);
        return $vips;
    }

    public static function all()
    {
        return CacheUtil::rememberForever('MemberVipList', function () {
            $records = ModelUtil::all(MemberVipSet::class, [], ['*'], ['sort', 'asc']);
            foreach ($records as $k => $v) {
                if (!$v['groupName']) {
                    $records[$k]['groupName'] = 'VIP';
                }
            }
            return $records;
        });
    }

    public static function update($id, $data)
    {
        ModelUtil::update(MemberVipSet::class, $id, $data);
        self::clearCache();
    }

    public static function map()
    {
        return CacheUtil::rememberForever('MemberVipMap', function () {
            $map = [];
            foreach (self::all() as $item) {
                $map[intval($item['id'])] = $item;
            }
            return $map;
        });
    }

    public static function vipsByIds($vipIds)
    {
        $map = self::map();
        $vips = [];
        foreach ($vipIds as $vipId) {
            if (isset($map[$vipId])) {
                $vips[] = $map[$vipId];
            }
        }
        return $vips;
    }

    public static function mapTitleWithGuest()
    {
        $map = [];
        $map[0] = '游客';
        foreach (self::map() as $k => $v) {
            $map[$k] = $v['title'];
        }
        return $map;
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

    public static function getMemberVipByIdCached($memberUserId, $key = null, $defaultValue = null)
    {
        $memberUser = MemberUtil::getCached($memberUserId);
        return self::getMemberVip($memberUser, $key, $defaultValue);
    }

    public static function getMemberVip($memberUser, $key = null, $defaultValue = null)
    {
        if (
            !empty($memberUser['vipId'])
            &&
            (empty($memberUser['vipExpire']) || strtotime($memberUser['vipExpire']) > time())
        ) {
            $vip = self::get($memberUser['vipId']);
        } else {
            $vip = self::get(null);
        }
        // 过期的用户，需要更新数据库
        if (!empty($memberUser['vipExpire']) && strtotime($memberUser['vipExpire']) <= time()) {
            $defaultVipId = self::defaultVipId();
            MemberUtil::update($memberUser['id'], [
                'vipId' => $defaultVipId,
                'vipExpire' => null
            ]);
            if ($defaultVipId != $memberUser['vipId']) {
                MemberUserVipChangeEvent::fire($memberUser['id'], $defaultVipId, $memberUser['vipId']);
            }
        }
        if (empty($vip)) {
            return null;
        }
        if (null === $key) {
            return $vip;
        }
        return isset($vip[$key]) ? $vip[$key] : $defaultValue;
    }

    public static function sortRecordsByVipOrder($records)
    {
        $vipSortMap = [0 => 0];
        foreach (MemberVipUtil::all() as $i => $v) {
            $vipSortMap[$v['id']] = $i + 1;
        };
        usort($records, function ($a, $b) use ($vipSortMap) {
            if (isset($vipSortMap[$a['vipId']]) && isset($vipSortMap[$b['vipId']])) {
                return $vipSortMap[$a['vipId']] - $vipSortMap[$b['vipId']];
            } else if (isset($vipSortMap[$a['vipId']])) {
                return -1;
            } else if (isset($vipSortMap[$b['vipId']])) {
                return 1;
            }
            return 0;
        });
        return $records;
    }

    /**
     * @return mixed|null
     * @deprecated delete at 2023-10-11
     */
    public static function getDefaultVip()
    {
        return self::defaultVip();
    }

    public static function defaultVip()
    {
        foreach (self::map() as $vipId => $vip) {
            if (!empty($vip['isDefault'])) {
                return $vip;
            }
        }
        return null;
    }


    public static function defaultVipId()
    {
        $vip = self::defaultVip();
        return $vip ? $vip['id'] : null;
    }

    public static function get($vipId, $key = null)
    {
        $map = self::map();
        if (null === $key) {
            if (isset($map[$vipId])) {
                return $map[$vipId];
            }
            return self::defaultVip();
        } else {
            if (isset($map[$vipId][$key])) {
                return $map[$vipId][$key];
            }
            $vip = self::defaultVip();
            if (isset($vip[$key])) {
                return $vip[$key];
            }
        }
        return null;
    }

    public static function calcExpire($oldExpire, $newVipId)
    {
        $newVip = self::get($newVipId);
        $newDays = ($newVip ? $newVip['vipDays'] : 0);
        $timestamp = time();
        $oldExpireTimestamp = max(strtotime($oldExpire), 0);
        if ($oldExpireTimestamp > time()) {
            $timestamp = $oldExpireTimestamp;
        }
        return date('Y-m-d', $timestamp + $newDays * 24 * 3600);
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
            $expire = date('Y-m-d H:i:s', max($oldVipExpireTimestamp, time()) + $newVip['vipDays'] * 24 * 3600);
            $price = $newVip['price'];
        } else {
            if ($oldVipExpireTimestamp > 0) {
                $type = '变更会员';
                $expireTimestamp = max(time() + $newVip['vipDays'] * 24 * 3600, $oldVipExpireTimestamp);
                $expire = date('Y-m-d H:i:s', $expireTimestamp);
                $price = $newVip['price'] * (($expireTimestamp - time()) / (24 * 3600)) / $newVip['vipDays'];
                // 老会员折算
                $oldLeftDays = max(0, intval(($oldVipExpireTimestamp - time()) / (24 * 3600)));
                $oldTotalDays = $oldVip['vipDays'];
                $oldPriceValue = ($oldTotalDays > 0 ? ($oldVip['price'] * $oldLeftDays / $oldTotalDays) : 0);
                $price = max(bcsub($price, $oldPriceValue, 2), 0.01);
            } else {
                $type = '新开会员';
                $expire = date('Y-m-d H:i:s', time() + $newVip['vipDays'] * 24 * 3600);
                $price = $newVip['price'];
            }
        }
        return Response::generate(0, 'ok', [
            'type' => $type,
            'expire' => $expire,
            'price' => $price,
        ]);

    }

    public static function rights()
    {
        return Cache::rememberForever('MemberVipRights', function () {
            $records = ModelUtil::all('member_vip_right', [], ['*'], ['sort', 'asc']);
            AssetsUtil::recordsFixFullOrDefault($records, 'image');
            ModelUtil::decodeRecordsJson($records, ['vipIds']);
            return $records;
        });
    }

    public static function openUsers()
    {
        $openUsersConfig = modstart_config('Member_VipOpenUsers', []);
        $records = [];
        foreach ($openUsersConfig as $u) {
            $records[] = [
                'name' => mb_substr($u['name'], 0, 2) . '******',
                'time' => $u['time'],
                'title' => $u['title'],
            ];
        }
        return $records;
    }


    public static function clearCache()
    {
        CacheUtil::forget('MemberVipList');
        CacheUtil::forget('MemberVipMap');
        CacheUtil::forget('MemberVipRights');
    }

    public static function accessCheck($recordId, $dailyStoreModel, $option = [])
    {
        $option = array_merge([
            'memberUserId' => null,
            'checkerBefore' => null,
        ], $option);
        if (null === $option['memberUserId']) {
            $option['memberUserId'] = MemberUser::id();
        }
        if ($option['checkerBefore']) {
            if (call_user_func_array($option['checkerBefore'], [$option['memberUserId'], $recordId])) {
                return true;
            }
        }
        $where = [
            'memberUserId' => $option['memberUserId'],
            'recordId' => $recordId,
        ];
        if (ModelUtil::exists($dailyStoreModel, $where)) {
            return true;
        }
        return false;
    }

    public static function accessQuotaConsume($recordId, $vipSetKeyPrefix, $dailyStoreModel, $option = [])
    {
        $option = array_merge([
            'memberUserId' => null,
            'vipSet' => null,
            'dailyCheck' => true,
            'monthlyCheck' => true,
            'yearlyCheck' => true,
            'checkerBefore' => null,
        ], $option);
        if (null === $option['memberUserId']) {
            $option['memberUserId'] = MemberUser::id();
        }
        $where = [
            'memberUserId' => $option['memberUserId'],
            'recordId' => $recordId,
        ];
        if ($option['checkerBefore']) {
            if (call_user_func_array($option['checkerBefore'], [$option['memberUserId'], $recordId])) {
                return Response::generateSuccess();
            }
        }
        if (ModelUtil::exists($dailyStoreModel, $where)) {
            return Response::generateSuccess();
        }
        if (null === $option['vipSet']) {
            $option['vipSet'] = self::getMemberVipByIdCached($option['memberUserId']);
        }
        if ($option['yearlyCheck']) {
            if (!array_key_exists($vipSetKeyPrefix . 'Yearly', $option['vipSet'])) {
                return Response::generate(-1, '年度额度不足:-1');
            }
            if ($option['vipSet'][$vipSetKeyPrefix . 'Yearly']) {
                $yearlyCount = ModelUtil::model($dailyStoreModel)
                    ->where('memberUserId', $option['memberUserId'])
                    ->where('day', '>=', date('Y-01-01'))
                    ->where('day', '<=', date('Y-12-31'))
                    ->count();
                if ($yearlyCount >= $option['vipSet'][$vipSetKeyPrefix . 'Yearly']) {
                    return Response::generate(-1, '年度额度不足');
                }
            }
        }
        if ($option['monthlyCheck']) {
            if (!array_key_exists($vipSetKeyPrefix . 'Monthly', $option['vipSet'])) {
                return Response::generate(-1, '月度额度不足:-1');
            }
            if ($option['vipSet'][$vipSetKeyPrefix . 'Monthly']) {
                $monthlyCount = ModelUtil::model($dailyStoreModel)
                    ->where('memberUserId', $option['memberUserId'])
                    ->where('day', '>=', date('Y-m-01'))
                    ->where('day', '<=', date('Y-m-t'))
                    ->count();
                if ($monthlyCount >= $option['vipSet'][$vipSetKeyPrefix . 'Monthly']) {
                    return Response::generate(-1, '月度额度不足');
                }
            }
        }
        if ($option['dailyCheck']) {
            if (!array_key_exists($vipSetKeyPrefix . 'Daily', $option['vipSet'])) {
                return Response::generate(-1, '日度额度不足:-1');
            }
            if ($option['vipSet'][$vipSetKeyPrefix . 'Daily']) {
                $dailyCount = ModelUtil::model($dailyStoreModel)
                    ->where('memberUserId', $option['memberUserId'])
                    ->where('day', date('Y-m-d'))
                    ->count();
                if ($dailyCount >= $option['vipSet'][$vipSetKeyPrefix . 'Daily']) {
                    return Response::generate(-1, '日度额度不足');
                }
            }
        }
        ModelUtil::insert($dailyStoreModel, [
            'memberUserId' => $option['memberUserId'],
            'recordId' => $recordId,
            'day' => date('Y-m-d'),
        ]);
        return Response::generate(0, 'ok');
    }

    public static function functions()
    {
        // 'memberVipFunctionVisible' => modstart_config('Member_VipFunctionVisible', []),
        //            'memberVipFunctions' => modstart_config('Member_VipFunctions', []),
        $functionVisible = modstart_config('Member_VipFunctionVisible', []);
        if (empty($functionVisible)) {
            return [];
        }
        $vipSets = [];
        foreach (self::all() as $vipSet) {
            if (in_array($vipSet['id'], $functionVisible)) {
                $vipSets[] = $vipSet;
            }
        }
        $functions = modstart_config('Member_VipFunctions', []);
        $results = [];
        $head = [];
        $head[] = '功能';
        foreach ($vipSets as $vipSet) {
            $head[] = $vipSet['title'];
        }
        $results[] = $head;
        foreach ($functions as $func) {
            $line = [];
            $line[] = $func['title'];
            foreach ($vipSets as $vipSet) {
                if (!isset($func['Vip' . $vipSet['id']])) {
                    $line[] = '-';
                } else {
                    $line[] = $func['Vip' . $vipSet['id']];
                }
            }
            $results[] = $line;
        }
        return $results;
    }
}
