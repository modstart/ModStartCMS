<?php

namespace Module\VisitStatistic\Model;

use Illuminate\Database\Eloquent\Model;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TimeUtil;

class VisitStatisticDailyReport extends Model
{
    protected $table = 'visit_statistic_daily_report';

    public static function report($startDay = null, $endDay = null)
    {
        if (TimeUtil::isDateEmpty($startDay)) {
            $startDay = date('Y-m-d', time() - 15 * TimeUtil::PERIOD_DAY);
        }
        if (TimeUtil::isDateEmpty($endDay)) {
            $endDay = date('Y-m-d', time());
        }

        $startTs = strtotime($startDay);
        $endTs = strtotime($endDay);

        $records = self::query()->where('day', '>=', $startDay)->where('day', '<=', $endDay)->get();
        $recordMap = [];
        foreach ($records as $record) {
            $recordMap[$record->day] = $record->toArray();
        }

        $today = strtotime(date('Y-m-d', time()));
        for ($t = $startTs; $t <= $endTs; $t += TimeUtil::PERIOD_DAY) {
            $day = date('Y-m-d', $t);
            if (!isset($recordMap[$day])) {
                if ($t <= $today) {
                    $recordMap[$day] = self::calcReport($day);
                    if ($t < $today) {
                        ModelUtil::insert('visit_statistic_daily_report', $recordMap[$day]);
                    }
                } else {
                    $recordMap[$day] = [
                        'day' => $day,
                        'uv' => 0,
                        'pv' => 0,
                    ];
                }
            }
        }

        return [
            'time' => array_keys($recordMap),
            'pv' => array_column($recordMap, 'pv'),
            'uv' => array_column($recordMap, 'uv'),
        ];
    }

    private static function calcReport($day)
    {
        $result = [
            'day' => $day,
            'uv' => 0,
            'pv' => 0,
        ];
        $result['pv'] = VisitStatisticItem::where('created_at', '>=', $day . ' 00:00:00')
            ->where('created_at', '<=', $day . ' 23:59:59')
            ->count();
        $result['uv'] = VisitStatisticItem::where('created_at', '>=', $day . ' 00:00:00')
            ->where('created_at', '<=', $day . ' 23:59:59')
            ->distinct('ip')
            ->count('ip');
        return $result;
    }
}
