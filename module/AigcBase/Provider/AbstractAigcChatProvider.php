<?php


namespace Module\AigcBase\Provider;


use ModStart\Core\Exception\BizException;
use Module\AigcBase\Type\AigcProviderType;

abstract class AbstractAigcChatProvider extends AbstractAigcProvider
{
    public function type()
    {
        return AigcProviderType::CHAT;
    }

    public function functions()
    {
        return [
            'chat' => '对话',
        ];
    }


    public function streamSupport()
    {
        return false;
    }

    protected function normalMsg($msg)
    {
        if (!is_array($msg)) {
            $msg = [
                'type' => 'text',
                'content' => $msg,
            ];
        }
        return $msg;
    }

    /**
     * @param $sessionId string 会话ID
     * @param $msg string|array 消息
     * @param $option
     * @return mixed
     */
    abstract function chat($sessionId, $msg, $option = []);

    public function chatStream($streamCallback, $sessionId, $msg, $option = [])
    {
        BizException::throws('未实现方法 AbstractAigcChatProvider.chatStream');
    }
}
