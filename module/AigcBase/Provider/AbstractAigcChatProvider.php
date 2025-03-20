<?php


namespace Module\AigcBase\Provider;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\HtmlUtil;
use Module\AigcBase\Type\AigcProviderType;
use Module\AigcBase\Util\AigcKeyPoolUtil;
use Module\Vendor\Markdown\MarkdownUtil;

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

    protected function chatGetContentOrFail($sessionId, $msg, $option)
    {
        if ($msg['type'] != 'text') {
            BizException::throws('机器人不能识别消息，请稍后再试');
        }
        $content = $msg['content'];
        if (!$option['markdown']) {
            $content = HtmlUtil::text($msg['content']);
        }
        BizException::throwsIfEmpty('消息内容为空', $content);
        return $content;
    }

    protected function chatResponse($sessionId, $content, $option)
    {
        if (!$option['markdown']) {
            $content = MarkdownUtil::convertToHtml($content);
        }
        return Response::generateSuccessData([
            'msg' => [
                'type' => 'text',
                'content' => $content,
            ]
        ]);
    }

    protected function chatResponseError($sessionId, $option)
    {
        return Response::generateSuccessData([
            'msg' => [
                'type' => 'text',
                'content' => '机器人太忙啦，请稍后再试'
            ]
        ]);
    }

    protected function chatPrepare($sessionId, $msg, $option)
    {
        if (!is_array($msg)) {
            $msg = [
                'type' => 'text',
                'content' => $msg,
            ];
        }
        $option = array_merge([
            // 是否是 Markdown 返回，默认为 false
            'markdown' => false,
        ], $option);;
        return [
            $sessionId,
            $msg,
            $option,
        ];
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
