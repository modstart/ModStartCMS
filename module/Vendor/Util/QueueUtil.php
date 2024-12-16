<?php

namespace Module\Vendor\Util;

class QueueUtil
{
    public static function isAsync()
    {
        $driver = config('queue.default');
        if ('sync' == $driver) {
            return false;
        }
        return true;
    }

    public static function queueRestartTip()
    {
        return "<div class='ub-alert warning'><i class='iconfont icon-warning'></i> 当前系统使用的是异步队列，修改该配置后需要重启队列。</div>";
    }
}
