<?php


namespace Module\Vendor\Provider\ContentVerify;


use ModStart\Core\Job\BaseJob;

class ContentVerifyJob extends BaseJob
{
    public $name;
    public $title;
    public $body;
    public $param;

    /**
     * 创建一个内容审核任务
     * @param $name string 审核模块名称
     * @param $param array 审核参数，内置参数 viewUrl成功后查看链接 processUrl审核处理链接
     * @param $title string 审核标题
     * @param $body string 审核内容
     */
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
