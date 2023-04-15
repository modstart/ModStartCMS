<?php


namespace Module\Vendor\Provider\ContentVerify;


use Illuminate\Support\Facades\Log;
use ModStart\Core\Input\Request;
use ModStart\Core\Job\BaseJob;

class ContentVerifyJob extends BaseJob
{
    public $name;
    public $title;
    public $body;
    public $param;

    public static function createQuick($name, $id, $title, $viewUrl = null)
    {
        self::create($name, [
            'id' => $id,
            'viewUrl' => $viewUrl,
        ], $title);
    }

    /**
     * 创建一个内容审核任务
     * @param $name string 审核模块名称
     * @param $param array 审核参数，内置参数 (viewUrl成功后查看链接,processUrl审核处理链接,domainUrl访问域名URL)
     * @param $title string 审核标题，通知标题自动变为 -> [审核]业务标题(审核标题)
     * @param $body string 审核内容
     */
    public static function create($name, $param, $title, $body = null)
    {
        if (!isset($param['domainUrl'])) {
            $param['domainUrl'] = Request::domainUrl();
        }
        if (isset($param['viewUrl'])) {
            // prepend http:// https://
            if (!preg_match('/^https?:\/\//', $param['viewUrl'])) {
                $param['viewUrl'] = $param['domainUrl'] . $param['viewUrl'];
            }
        }
        $job = new static();
        $job->name = $name;
        $job->param = $param;
        $job->title = $title;
        $job->body = $body;
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        $provider = ContentVerifyBiz::getByName($this->name);
        if (empty($provider)) {
            Log::info('Vendor.ContentVerifyJob.UnknownProvider - ' . $this->name);
            return;
        }
        $provider->run($this->param, $this->title, $this->body);
    }
}
