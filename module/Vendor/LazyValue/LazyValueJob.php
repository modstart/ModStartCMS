<?php

namespace Module\Vendor\LazyValue;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Job\BaseJob;
use Module\Vendor\Log\Logger;

class LazyValueJob extends BaseJob
{
    private $key;
    private $param;
    private $cacheSeconds;

    public function __construct($key, $param, $cacheSeconds)
    {
        $this->key = $key;
        $this->param = $param;
        $this->cacheSeconds = $cacheSeconds;
    }

    public static function create($key, $param, $cacheSeconds)
    {
        $job = new LazyValueJob($key, $param, $cacheSeconds);
        $job->onQueue('LazyValueJob');
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public static function createRefresh($key, $param, $cacheSeconds)
    {
        $job = new LazyValueJob($key, $param, $cacheSeconds);
        $job->onQueue('LazyValueJobRefresh');
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        $start = time();
        Logger::info('LazyValueJob', 'Start', [$this->key, $this->cacheSeconds, $this->param]);
        $roots = config('app.lazy_value_processor_roots', []);
        if (empty($roots)) {
            throw new \Exception('LazyValueJob.Error : you should define lazy_value_processor_roots in config/app.php file');
        }
        $found = false;
        foreach ($roots as $root) {
            $cls = $root . '\\' . $this->key . 'Processor';
            if (class_exists($cls)) {
                $ins = new $cls();
                $value = $ins->execute($this->param);
                $found = true;
            }
        }
        if (!$found) {
            throw new \Exception('LazyValueJob.Error : could not found processor ' . $this->key);
        }
        Logger::info('LazyValueJob', 'Result', $value);
        ModelUtil::update('lazy_value', ['key' => $this->key, 'param' => json_encode($this->param)], [
            'expire' => time() + $this->cacheSeconds,
            'value' => json_encode($value),
        ]);
        Logger::info('LazyValueJob', 'End');
    }
}
