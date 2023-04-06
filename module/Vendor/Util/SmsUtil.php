<?php

namespace Module\Vendor\Util;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use Module\Vendor\Provider\SmsSender\SmsSenderProvider;

/**
 * Class SmsUtil
 * @package Module\Vendor\Sms
 */
class SmsUtil
{
    public static function calcNumber($content)
    {
        return ceil(mb_strlen($content) / 67);
    }

    public static function parseContent($template, $values = array())
    {
        $param1 = [];
        $param2 = [];
        foreach ($values as $k => $v) {
            $param1[] = '{' . $k . '}';
            $param2[] = $v;
        }
        return str_replace($param1, $param2, $template);
    }

    public static function parseTemplateParam($template)
    {
        preg_match_all('/\\{(.*?)\\}/', $template, $mat);
        return $mat[1];
    }

    public static function replaceTemplate($template, $callbackOrBorder = '#')
    {
        $param = self::parseTemplateParam($template);
        foreach ($param as $v) {
            if (is_string($callbackOrBorder)) {
                $template = str_replace('{' . $v . '}', $callbackOrBorder . $v . $callbackOrBorder, $template);
            } else {
                $template = str_replace('{' . $v . '}', call_user_func($callbackOrBorder, $v), $template);
            }
        }
        return $template;
    }

    /**
     * @return \string[][]
     * @deprecated delete after 2023-09-23
     */
    public static function templates()
    {
        $templates = [
            [
                'name' => 'verify',
                'title' => '验证码',
                'desc' => '验证码模板变量为 code'
            ]
        ];
        return $templates;
    }

    /**
     * @param $phone
     * @param $template
     * @param array $templateData
     * @return array
     * @throws BizException
     * @deprecated use SmsSendJob delete after 2023-09-23
     */
    public static function send($phone, $template, $templateData = [])
    {
        $provider = app()->config->get('SmsSenderProvider');
        BizException::throwsIfEmpty('短信发送未设置', $provider);
        $ret = SmsSenderProvider::get($provider)->send($phone, $template, $templateData);
        BizException::throwsIfResponseError($ret);
        return Response::generateSuccess();
    }

}
