<?php


namespace Module\AigcBase\Provider;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\HtmlUtil;
use Module\AigcBase\Type\AigcProviderType;
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
        if (empty($option['markdown'])) {
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
            'isError' => true,
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
            // 系统提示词
            'systemPrompt' => null,
            // 是否是 Markdown 返回，默认为 false
            'markdown' => false,
            // 是否推理，默认为非推理模式
            'reasoning' => false,
            // 推理强度（none|low|medium|high），显式指定时优先于 reasoning 使用
            'reasoning_effort' => null,
        ], $option);
        return [
            $sessionId,
            $msg,
            $option,
        ];
    }

    /**
     * 计算模型推理强度参数值（供 LLMPX 等支持推理的 API 使用）
     * 显式指定 reasoning_effort 时直接返回；否则根据是否推理（reasoning）返回 none / high
     * @param array $option chatPrepare 处理后的 option
     * @return string none|low|medium|high
     */
    protected function chatReasoningEffort($option)
    {
        if (!empty($option['reasoning_effort'])) {
            return $option['reasoning_effort'];
        }
        return empty($option['reasoning']) ? 'none' : 'high';
    }

    /**
     * @param $sessionId string 会话ID
     * @param $msg string|array 消息
     * @param $option
     * @example
     *  $option = [
     *    'systemPrompt' => 'You are a helpful assistant.',
     *  ]
     */
    abstract function chat($sessionId, $msg, $option = []);

    /**
     * @param $streamCallback
     * @param $sessionId
     * @param $msg
     * @param $option
     * @return void
     * @throws BizException
     * @example
     * $option = [
     *   'systemPrompt' => 'You are a helpful assistant.',
     * ]
     */
    public function chatStream($streamCallback, $sessionId, $msg, $option = [])
    {
        BizException::throws('未实现方法 AbstractAigcChatProvider.chatStream');
    }
}
