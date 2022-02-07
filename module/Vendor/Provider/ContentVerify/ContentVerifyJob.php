<?php


namespace Module\Vendor\Provider\ContentVerify;


use ModStart\Core\Job\BaseJob;

class ContentVerifyJob extends BaseJob
{
    public $name;
    public $body;
    public $param;

    public static function create($name, $param, $body)
    {
        $job = new static();
        $job->name = $name;
        $job->param = $param;
        $job->body = $body;
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        $provider = ContentVerifyProvider::get($this->name);
        $provider->run($this->param, $this->body);
    }
}