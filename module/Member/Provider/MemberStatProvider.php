<?php

namespace Module\Member\Provider;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Module\Analysis\Provider\AbstractStatProvider;
use Module\Member\Model\MemberUser;

/**
 * Member 用户统计 Provider。
 *
 * 向 /api/analysis/stat 接口输出用户注册与留存统计数据。
 */
class MemberStatProvider extends AbstractStatProvider
{
    public function name()
    {
        return 'member';
    }

    public function title()
    {
        return '用户统计';
    }

    public function enable()
    {
        try {
            return Schema::hasTable('member_user');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function statMeta()
    {
        return [
            '注册用户数' => 'latest',
            '注册用户增长' => 'sum',
            '7日留存' => 'latest',
            '30日留存' => 'latest',
        ];
    }

    public function statByDay($startDay, $endDay, $days)
    {
        $growthMap = $this->getMemberUserGrowthMap($startDay, $endDay);
        $totalMap = $this->getMemberUserTotalMap($days, $growthMap);
        $retention7Map = $this->getRetentionMap($startDay, $endDay, $days, 7);
        $retention30Map = $this->getRetentionMap($startDay, $endDay, $days, 30);

        $result = [];
        foreach ($days as $day) {
            $result[$day] = [
                '注册用户数' => isset($totalMap[$day]) ? $totalMap[$day] : 0,
                '注册用户增长' => isset($growthMap[$day]) ? $growthMap[$day] : 0,
                '7日留存' => isset($retention7Map[$day]) ? $retention7Map[$day] : 0,
                '30日留存' => isset($retention30Map[$day]) ? $retention30Map[$day] : 0,
            ];
        }

        return $result;
    }

    private function getMemberUserGrowthMap($startDay, $endDay)
    {
        $rows = MemberUser::query()
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as c'))
            ->where('created_at', '>=', $startDay . ' 00:00:00')
            ->where('created_at', '<=', $endDay . ' 23:59:59')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        $map = [];
        foreach ($rows as $row) {
            $map[$row->d] = (int)$row->c;
        }
        return $map;
    }

    private function getMemberUserTotalMap($days, $growthMap)
    {
        $firstDay = $days[0];
        $baseTotalCount = MemberUser::query()
            ->where('created_at', '<', $firstDay . ' 00:00:00')
            ->count();
        $map = [];
        $cumulative = $baseTotalCount;
        foreach ($days as $day) {
            $cumulative += isset($growthMap[$day]) ? $growthMap[$day] : 0;
            $map[$day] = $cumulative;
        }
        return $map;
    }

    /**
     * 留存：以注册满 N 天的历史 cohort 为基线。
     * retentionN[day] = 注册日为 (day-N) 的用户中，注册后第 N 天（即 day 当天）仍活跃的比例。
     * 活跃信号统一使用 MemberTickHub 打点（客户端获取配置时记录，name=AppActive，按 day 去重，
     * 每用户每天一条），MemberTickHub 未启用时无活跃信号（留存为 0）。
     */
    private function getRetentionMap($startDay, $endDay, $days, $n)
    {
        $startTs = strtotime($startDay);
        $startDayMinusN = date('Y-m-d', $startTs - $n * 86400);

        $registerUsersByDay = [];
        $users = MemberUser::query()
            ->select('id', DB::raw('DATE(created_at) as d'))
            ->where('created_at', '>=', $startDayMinusN . ' 00:00:00')
            ->where('created_at', '<=', $endDay . ' 23:59:59')
            ->get();
        foreach ($users as $u) {
            $uid = (int)$u->id;
            $d = $u->d;
            if (!isset($registerUsersByDay[$d])) {
                $registerUsersByDay[$d] = [];
            }
            $registerUsersByDay[$d][$uid] = true;
        }

        // 活跃信号：member_tick_hub 客户端打点（name=AppActive，按 day 字段判断当天是否活跃）
        $activeUsersByDay = [];
        try {
            if (modstart_module_enabled('MemberTickHub')) {
                $class = 'Module\MemberTickHub\Model\MemberTickHub';
                if (class_exists($class)) {
                    $logs = $class::query()
                        ->select(DB::raw('DISTINCT memberUserId'), 'day')
                        ->where('name', 'AppActive')
                        ->whereNotNull('memberUserId')
                        ->where('day', '>=', $startDayMinusN)
                        ->where('day', '<=', $endDay)
                        ->get();
                    foreach ($logs as $log) {
                        if (empty($log->day)) {
                            continue;
                        }
                        $activeUsersByDay[$log->day][(int)$log->memberUserId] = true;
                    }
                }
            }
        } catch (\Exception $e) {
            // MemberTickHub 不可用时静默降级
        }

        $map = [];
        foreach ($days as $day) {
            $regDay = date('Y-m-d', strtotime($day) - $n * 86400);
            $cohortCount = isset($registerUsersByDay[$regDay]) ? count($registerUsersByDay[$regDay]) : 0;
            $activeCount = 0;
            if ($cohortCount > 0 && isset($registerUsersByDay[$regDay]) && isset($activeUsersByDay[$day])) {
                foreach ($registerUsersByDay[$regDay] as $uid => $v) {
                    if (isset($activeUsersByDay[$day][$uid])) {
                        $activeCount++;
                    }
                }
            }
            $map[$day] = $cohortCount > 0 ? round($activeCount / $cohortCount * 100, 2) : 0;
        }
        return $map;
    }
}