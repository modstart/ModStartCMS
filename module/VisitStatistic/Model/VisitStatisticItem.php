<?php

namespace Module\VisitStatistic\Model;

use Illuminate\Database\Eloquent\Model;

class VisitStatisticItem extends Model
{
    protected $table = 'visit_statistic_item';

    public static function deleteHistory($days)
    {
        self::query()->where('created_at', '<', date('Y-m-d H:i:s', time() - $days * 86400))->delete();
    }
}
