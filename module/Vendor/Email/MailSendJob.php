<?php

namespace Module\Vendor\Email;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Job\BaseJob;
use Module\Vendor\Log\Logger;

class MailSendJob extends BaseJob
{
    public $email;
    public $subject;
    public $template;
    public $templateData = [];
    public $emailUserName = null;
    public $option = [];
    public $module;

    public static function create($email, $subject, $template, $templateData = [], $emailUserName = null, $option = [], $delay = 0, $module = null)
    {
        $driver = app()->config->get('EmailSenderDriver');
        BizException::throwsIfEmpty('邮箱发送未配置', $driver);

        $job = new MailSendJob();
        $job->email = $email;
        $job->subject = $subject;
        $job->template = $template;
        $job->templateData = $templateData;
        $job->emailUserName = $emailUserName;
        $job->option = $option;
        $job->module = $module;
        $job->onQueue('DefaultJob');
        if ($delay > 0) {
            $job->delay($delay);
        }
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        $driver = app()->config->get('EmailSenderDriver');
        
        $instance = app($driver);
        Logger::info('Email', 'Start', $this->email . ' -> ' . $this->subject . ' -> ' . $this->template);
        $ret = $instance->send($this->email, $this->subject, $this->template, $this->templateData, $this->emailUserName, $this->option, $this->module);
        BizException::throwsIfResponseError($ret);
        Logger::info('Email', 'End', $this->email . ' -> ' . $this->subject);
    }
}
