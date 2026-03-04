<?php


namespace Module\Vendor\Provider\Schedule;


use Illuminate\Console\Scheduling\Schedule;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Core\Util\TimeUtil;
use Module\Vendor\Model\ScheduleRun;

/**
 * Class ScheduleProvider
 * @package Module\Vendor\Provider\Schedule
 * @since 1.5.0
 */
class ScheduleProvider
{
    /**
     * @var AbstractScheduleProvider[]
     * @deprecated delete at 2023-10-10
     */
    private static $instances = [
    ];

    /**
     * @param $provider
     * @deprecated delete at 2023-10-10
     */
    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    /**
     * @return AbstractScheduleProvider[]
     * @deprecated delete at 2023-10-10
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
        $processed = false;
        foreach (ScheduleBiz::all() as $provider) {
            if ($provider->name() == $name) {
                call_user_func([$provider, 'run']);
                $processed = true;
                break;
            }
        }
        if (!$processed) {
            echo "ScheduleProvider.callByName - $name not found\n";
        }
    }

    public static function call(Schedule $schedule)
    {
        if (!isset($_SERVER['argv'][1]) || $_SERVER['argv'][1] != 'schedule:run') {
            return;
        }
        foreach (ScheduleBiz::all() as $provider) {
            //LogUtil::info('ScheduleProvider.schedule - ' . $provider->title() . ' - ' . $provider->cron());
            self::callOne($schedule, $provider);
        }
        self::cleanHistory();
    }

    private static function callOne(Schedule $schedule, AbstractScheduleBiz $provider)
    {
        $schedule->call(function () use ($provider) {
            //LogUtil::info('ScheduleProvider.schedule.run - ' . $provider->title() . ' - ' . $provider->cron());
            $data = [];
            $data['name'] = get_class($provider);
            $data['startTime'] = date('Y-m-d H:i:s');
            $data['status'] = RunStatus::RUNNING;
            $data = ModelUtil::insert(ScheduleRun::class, $data);
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
            ModelUtil::update(ScheduleRun::class, $dataId, $data);
        })->cron($provider->cron());
    }

    private static function cleanHistory()
    {
        if (RandomUtil::percent(10)) {
            ModelUtil::model(ScheduleRun::class)
                ->where('created_at', '<', date('Y-m-d H:i:s', time() - TimeUtil::PERIOD_DAY * 7))
                ->delete();
        }
    }
}
