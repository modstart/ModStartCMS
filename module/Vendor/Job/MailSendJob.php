<?php

namespace Module\Vendor\Job;

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
    public $html;
    public $type;

    private static function checkConfig()
    {
        $provider = app()->config->get('EmailSenderProvider');
        BizException::throwsIfEmpty('邮箱发送未设置', $provider);
    }

    public static function createHtml($email, $subject, $html, $emailUserName = null, $option = [], $delay = 0)
    {
        self::checkConfig();
        $job = new static();
        $job->type = 'html';
        $job->email = $email;
        $job->subject = $subject;
        $job->html = $html;
        $job->emailUserName = $emailUserName;
        $job->option = $option;
        if ($delay > 0) {
            $job->delay($delay);
        }
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public static function createModule($module, $template, $email, $subject, $templateData = [], $emailUserName = null, $option = [], $delay = 0)
    {
        self::create($email, $subject, $template, $templateData, $emailUserName, $option, $delay, $module);
    }

    public static function create($email, $subject, $template, $templateData = [], $emailUserName = null, $option = [], $delay = 0, $module = null)
    {
        self::checkConfig();
        $job = new static();
        $job->email = $email;
        $job->subject = $subject;
        $job->template = $template;
        $job->templateData = $templateData;
        $job->emailUserName = $emailUserName;
        $job->option = $option;
        $job->module = $module;
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
        Logger::info('Email', 'Start', $this->email . ' - ' . $this->subject . ' - ' . $this->template);

        switch ($this->type) {
            case 'html':
                $html = $this->html;
                break;
            default:
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
                $html = View::make($view, $this->templateData)->render();
                break;
        }
        BizException::throwsIfEmpty('MailSendJob.HtmlEmpty', $html);
        $ret = $instance->send($this->email, $this->emailUserName, $this->subject, $html);
        BizException::throwsIfResponseError($ret);
        Logger::info('Email', 'End', $this->email . ' - ' . $this->subject);
    }
}
