<?php


namespace Module\Vendor\Provider\Schedule;


use Illuminate\Console\Scheduling\Schedule;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Core\Util\TimeUtil;
use Illuminate\Support\Facades\Log;

/**
 * Class ScheduleProvider
 * @package Module\Vendor\Provider\Schedule
 * @since 1.5.0
 */
class ScheduleProvider
{
    /**
     * @var AbstractScheduleProvider[]
     */
    private static $instances = [
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractScheduleProvider[]
     */
    public static function all()
    {
        foreach (self::$instances as $k => $v) {
            if ($v instanceof \Closure) {
                self::$instances[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$instances[$k] = app($v);
            }
        }
        return self::$instances;
    }

    public static function callByName($name)
    {
        foreach (self::all() as $provider) {
            if ($provider->name() == $name) {
                call_user_func([$provider, 'run']);
            }
        }
    }

    public static function call(Schedule $schedule)
    {
        $autoCleanHistory = true;
        foreach (ScheduleProvider::all() as $provider) {
            // Log::info('ScheduleProvider.schedule - ' . $provider->title() . ' - ' . $provider->cron());
            /** @var AbstractScheduleProvider $provider */
            $schedule->call(function () use ($provider, &$autoCleanHistory) {
                $data = [];
                $data['name'] = get_class($provider);
                $data['startTime'] = date('Y-m-d H:i:s');
                $data['status'] = RunStatus::RUNNING;
                $data = ModelUtil::insert('schedule_run', $data);
                $dataId = $data['id'];
                $data = [];
                try {
                    $result = call_user_func([$provider, 'run']);
                    $data['result'] = StrUtil::mbLimit($result, 200);
                    $data['status'] = RunStatus::SUCCESS;
                } catch (\Exception $e) {
                    $data['result'] = StrUtil::mbLimit($e->getMessage(), 200);
                    $data['status'] = RunStatus::FAILED;
                }
                $data['endTime'] = date('Y-m-d H:i:s');
                ModelUtil::update('schedule_run', $dataId, $data);
                // 只保留最近7天的运行日志
                if ($autoCleanHistory) {
                    $autoCleanHistory = false;
                    if (RandomUtil::percent(10)) {
                        ModelUtil::model('schedule_run')
                            ->where('created_at', '<', date('Y-m-d H:i:s', time() - TimeUtil::PERIOD_DAY * 7))
                            ->delete();
                    }
                }
            })->cron($provider->cron());
        }
    }
}
