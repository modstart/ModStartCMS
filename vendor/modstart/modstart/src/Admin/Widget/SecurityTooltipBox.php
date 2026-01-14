<?php

namespace ModStart\Admin\Widget;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Widget\AbstractWidget;
use Module\Vendor\Model\ScheduleRun;

class SecurityTooltipBox extends AbstractWidget
{
    protected $view = 'modstart::admin.widget.securityTooltipBox';

    protected function variables()
    {
        $queueDriver = config('env.QUEUE_DRIVER');
        $queueDelaySize = 0;
        if ($queueDriver) {
            if ('database' == $queueDriver) {
                $queueDelaySize = DB::table('jobs')->where('available_at', '<', time() - 600)->count();
            }
        }
        $scheduleRunLastRun = time();
        if (ModelManageUtil::hasTable('schedule_run')) {
            $lastOne = ScheduleRun::orderBy('id', 'desc')->first();
            if ($lastOne) {
                $scheduleRunLastRun = strtotime($lastOne->created_at);
            }
        }
        return [
            'attributes' => $this->formatAttributes(),
            'queueDelaySize' => $queueDelaySize,
            'scheduleRunLastRun' => $scheduleRunLastRun,
        ];
    }
}
