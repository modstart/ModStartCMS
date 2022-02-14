<?php


namespace Module\Vendor\Provider\ContentVerify;


use ModStart\Core\Job\BaseJob;

class ContentVerifyJob extends BaseJob
{
    public $name;
    public $title;
    public $body;
    public $param;

    public static function create($name, $param, $title, $body = null)
    {
        $job = new static();
        $job->name = $name;
        $job->param = $param;
        $job->title = $title;
        $job->body = $body;
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        $provider = ContentVerifyProvider::get($this->name);
        $provider->run($this->param, $this->title, $this->body);
    }
}
