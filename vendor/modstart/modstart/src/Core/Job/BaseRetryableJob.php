<?php


namespace ModStart\Core\Job;

use Illuminate\Foundation\Bus\DispatchesJobs;

class BaseRetryableJob extends BaseJob
{
    use DispatchesJobs;

    public $_retryTimes = [10, 30, 60, 600, 3600];
    public $_retryIndex = 0;

    public function retryNext()
    {
        if ($this->_retryIndex >= count($this->_retryTimes)) {
            return false;
        }
        $delay = $this->_retryTimes[$this->_retryIndex];
        $jobCls = get_class($this);
        $job = new $jobCls();
        $job->_retryTimes = $this->_retryTimes;
        $job->_retryIndex = $this->_retryIndex + 1;
        foreach (get_object_vars($this) as $k => $v) {
            if (in_array($k, ['job', '_retryTimes', '_retryIndex'])) {
                continue;
            }
            $job->{$k} = $v;
        }
        $job->delay($delay);
        $this->dispatch($job);
        return true;
    }
}