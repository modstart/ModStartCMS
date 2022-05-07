<?php


namespace Module\Vendor\QuickRun\Crawl;


abstract class AbstractCrawler
{
    /**
     * @var BaseQueue
     */
    protected $queue = null;
    protected $handlers = [];

    protected $delay = [
        'min' => 0,
        'max' => 0,
    ];

    public function setDelay($minMS, $maxMS = null)
    {
        if (null === $maxMS) {
            $maxMS = $minMS;
        }
        $this->delay['min'] = $minMS;
        $this->delay['max'] = $maxMS;
    }


    public function init()
    {
        if (null == $this->queue) {
            $this->queue = new ArrayQueue();
        }
    }

    public function register($handler, $successCallable, $failCallable = null)
    {
        $this->handlers[$handler] = [
            $successCallable,
            $failCallable,
        ];
    }

    public function dispatch($handler, $param = [], $id = null)
    {
        $this->queue->append($handler, $param, $id);
    }

    public function onFinish()
    {

    }

    public function name()
    {
        return class_basename(static::class);
    }

    public function logInfo($msg, $data = null)
    {
        $str = [];
        $str[] = date('Y-m-d H:i:s');
        $str[] = '[' . $this->name() . ']';
        $str[] = $msg;
        if (null != $data) {
            if (is_string($data)) {
                $str[] = $data;
            } else {
                $str[] = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
        }
        echo join(' - ', $str) . "\n";
        // Log::info(join(' - ', $str));
    }

    public function logError($msg, $data = null)
    {
        $str = [];
        $str[] = date('Y-m-d H:i:s');
        $str[] = '[' . $this->name() . ']';
        $str[] = $msg;
        if (null != $data) {
            $str[] = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        echo join(' - ', $str) . "\n";
        // Log::error(join(' - ', $str));
    }

    public function run()
    {
        while (true) {
            $job = $this->queue->poll();
            if (empty($job)) {
                $this->onFinish();
                $this->logInfo("End");
                break;
            }
            if (!isset($this->handlers[$job['handler']])) {
                $this->logError("Handler $job[handler] not registered", $job['param']);
                continue;
            }
            $successCallable = $this->handlers[$job['handler']][0];
            $failCallable = $this->handlers[$job['handler']][1];
            $id = $job['id'];
            if ($id === null) {
                if (is_string($job['param']) || is_numeric($job['param'])) {
                    $id = $job['param'];
                } else if (is_array($job['param'])) {
                    $ids = [];
                    foreach ($job['param'] as $k => $v) {
                        if (is_string($v) || is_numeric($v)) {
                        } else {
                            $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                        }
                        $ids[] = "$k:$v";
                    }
                    $id = join(",", $ids);
                } else {
                    $id = json_encode($job['param'], JSON_UNESCAPED_UNICODE);
                }
            }
            try {
                call_user_func_array($successCallable, [$this, $job['param'], $job['id']]);
                $this->logInfo("Execute $job[handler] $id");
            } catch (\Exception $e) {
                var_dump($job);
                $this->logInfo("Execute $job[handler] $id", $e->getMessage());
                if (null !== $failCallable) {
                    try {
                        $failCallable($this, $job['param'], $job['id']);
                    } catch (\Exception $e) {
                        $this->logInfo("Execute Fail for Error Handler $job[handler] $id", $e->getMessage());
                    }
                }
            }
            if ($this->delay['max'] > 0) {
                usleep(rand($this->delay['min'], $this->delay['max']) * 1000);
            }
        }
    }

    abstract public function setup();
}
