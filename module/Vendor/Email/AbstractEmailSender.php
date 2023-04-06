<?php


namespace Module\Vendor\Email;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;

/**
 * Class AbstractEmailSender
 * @package Module\Vendor\Email
 * @deprecated delete at 2023-10-04
 */
abstract class AbstractEmailSender
{
    abstract protected function sendExecute($email, $emailUserName, $subject, $content, $param = []);

    public function send($email, $subject, $template, $templateData = [], $emailUserName = null, $param = [], $module = null)
    {
        $view = $template;
        if (!view()->exists($view)) {
            $view = 'theme.' . modstart_config()->getWithEnv('siteTemplate', 'default') . '.mail.' . $template;
            if (!view()->exists($view)) {
                $view = 'theme.default.mail.' . $template;
                if (!view()->exists($view)) {
                    if ($module) {
                        $view = 'module::' . $module . '.View.mail.' . $template;
                    }
                    if (!view()->exists($view)) {
                        $view = 'module::Vendor.View.mail.' . $template;
                    }
                }
            }
        }

        if (!view()->exists($view)) {
            throw new \Exception('mail view not found : ' . $view);
        }

        if (null === $emailUserName) {
            $emailUserName = $email;
        }

        $content = View::make($view, $templateData)->render();

        try {
            $ret = $this->sendExecute($email, $emailUserName, $subject, $content, $param);
            BizException::throwsIfResponseError($ret);
            return Response::generateSuccess();
        } catch (BizException $e) {
            return Response::generateError($e->getMessage());
        }
    }
}
