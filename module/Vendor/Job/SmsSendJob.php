<?php


namespace Module\Vendor\Job;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Job\BaseJob;
use Module\Vendor\Log\Logger;
use Module\Vendor\Provider\SmsSender\SmsSenderProvider;

class SmsSendJob extends BaseJob
{
    public $phone;
    public $template;
    public $templateData;

    public static function create($phone, $template, $templateData)
    {
        $job = new static();
        $job->phone = $phone;
        $job->template = $template;
        $job->templateData = $templateData;
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        $logData = $this->phone . ' - ' . $this->template . ' - ' . json_encode($this->templateData, JSON_UNESCAPED_UNICODE);
        Logger::info('Sms', 'Start', $logData);
        $provider = app()->config->get('SmsSenderProvider');
        try {
            BizException::throwsIfEmpty('短信发送未设置', $provider);
            $ret = SmsSenderProvider::get($provider)->send($this->phone, $this->template, $this->templateData);
            BizException::throwsIfResponseError($ret);
            Logger::info('Sms', 'End', $this->phone . ' - ' . json_encode($ret, JSON_UNESCAPED_UNICODE));
        } catch (BizException $e) {
            Logger::error('Sms', 'Error', $this->phone . ' - ' . $e->getMessage());
        }
    }
}
