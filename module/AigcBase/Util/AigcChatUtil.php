<?php


namespace Module\AigcBase\Util;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use Module\AigcBase\Provider\AbstractAigcChatProvider;
use Module\AigcBase\Provider\AigcChatProvider;

class AigcChatUtil
{
    /**
     * 调用AI对话
     *
     * @param string $systemPrompt 系统提示词
     * @param string $userPrompt 用户消息
     * @param array $option 选项
     *   - driver: 直接指定驱动全名（优先级最高）
     *   - driverKey: 从配置读取驱动的 config key，默认 AigcBase_AdminDefaultChatDriver
     * @return array Response格式
     * @throws BizException
     */
    public static function chat($systemPrompt, $userPrompt, $option = [])
    {
        $driver = isset($option['driver']) ? $option['driver'] : null;
        if (empty($driver)) {
            $driverKey = isset($option['driverKey']) ? $option['driverKey'] : 'AigcBase_AdminDefaultChatDriver';
            $driver = modstart_config($driverKey);
        }
        if (!$driver) {
            BizException::throws('机器人没有配置，请在 后台→系统设置→AI平台对接→功能设置 中配置');
        }
        /** @var AbstractAigcChatProvider $provider */
        $provider = AigcChatProvider::getByFullName($driver);
        if (empty($provider)) {
            BizException::throws('机器人没有配置');
        }
        $chatOption = array_merge($option, [
            'systemPrompt' => $systemPrompt,
            'markdown' => false,
        ]);
        unset($chatOption['driver']);
        unset($chatOption['driverKey']);
        $ret = $provider->chat(uniqid('chat_', true), $userPrompt, $chatOption);
        return $ret;
    }

    /**
     * 调用AI对话并返回文本内容
     *
     * @param string $systemPrompt 系统提示词
     * @param string $userPrompt 用户消息
     * @param array $option 选项（同 chat()）
     * @return string
     * @throws BizException
     */
    public static function chatText($systemPrompt, $userPrompt, $option = [])
    {
        $ret = self::chat($systemPrompt, $userPrompt, array_merge($option, ['markdown' => true]));
        if (!Response::isSuccess($ret)) {
            BizException::throws('AI对话失败：' . (isset($ret['msg']) ? $ret['msg'] : '未知错误'));
        }
        if (!empty($ret['data']['isError'])) {
            BizException::throws('AI对话失败');
        }
        $content = isset($ret['data']['msg']['content']) ? $ret['data']['msg']['content'] : '';
        return trim(strip_tags($content));
    }
}
