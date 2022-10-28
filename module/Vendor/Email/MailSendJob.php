<?php

namespace Module\Vendor\Email;

use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Job\BaseJob;
use Module\Vendor\Log\Logger;
use Module\Vendor\Provider\MailSender\AbstractMailSenderProvider;
use Module\Vendor\Provider\MailSender\MailSenderProvider;

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
        $provider = app()->config->get('EmailSenderProvider');
        BizException::throwsIfEmpty('邮箱发送未配置', $provider);

        $job = new MailSendJob();
        $job->email = $email;
        $job->subject = $subject;
        $job->template = $template;
        $job->templateData = $templateData;
        $job->emailUserName = $emailUserName;
        $job->option = $option;
        $job->module = $module;
        // $job->onQueue('DefaultJob');
        if ($delay > 0) {
            $job->delay($delay);
        }
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public function handle()
    {
        $provider = app()->config->get('EmailSenderProvider');
        /** @var AbstractMailSenderProvider $instance */
        $instance = MailSenderProvider::get($provider);
        Logger::info('Email', 'Start', $this->email . ' -> ' . $this->subject . ' -> ' . $this->template);

        $view = $this->template;
        if (!view()->exists($view)) {
            $view = 'theme.' . modstart_config()->getWithEnv('siteTemplate', 'default') . '.mail.' . $this->template;
            if (!view()->exists($view)) {
                $view = 'theme.default.mail.' . $this->template;
                if (!view()->exists($view)) {
                    if ($this->module) {
                        $view = 'module::' . $this->module . '.View.mail.' . $this->template;
                    }
                    if (!view()->exists($view)) {
                        $view = 'module::Vendor.View.mail.' . $this->template;
                    }
                }
            }
        }
        if (!view()->exists($view)) {
            throw new \Exception('mail view not found : ' . $view);
        }
        if (null === $this->emailUserName) {
            $this->emailUserName = $this->email;
        }
        $content = View::make($view, $this->templateData)->render();
        $ret = $instance->send($this->email, $this->emailUserName, $this->subject, $content);
        BizException::throwsIfResponseError($ret);
        Logger::info('Email', 'End', $this->email . ' -> ' . $this->subject);
    }
}
