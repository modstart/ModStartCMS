<?php


namespace Module\Vendor\Provider\ContentVerify;


use Illuminate\Support\Str;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Form\Form;
use Module\Vendor\Provider\CensorImage\CensorImageProvider;
use Module\Vendor\Provider\CensorText\CensorTextProvider;
use Module\Vendor\Provider\Notifier\NotifierProvider;

/**
 * Class AbstractContentVerifyProvider
 * @package Module\Vendor\Provider\ContentVerify
 * @deprecated delete at 2023-10-10
 */
abstract class AbstractContentVerifyProvider
{
    /**
     * 业务唯一标识
     * @return string
     * @example post
     */
    abstract public function name();

    /**
     * 业务标题
     * @return string
     * @example 文章
     */
    abstract public function title();

    /**
     * 是否自动审核完成
     * @param $param
     * @return boolean
     */
    public function verifyAutoProcess($param)
    {
        return false;
    }

    /**
     * 待审核数量
     * @return int
     * @example return ModelUtil::count('post', ['status' => PostStatus::VERIFYING])
     */
    abstract public function verifyCount();

    /**
     * 审核权限规则（用户首页是否显示待审核数量的权限判断）
     * @return string
     * @example '\Module\Post\Admin\Controller\PostController@verifyList'
     */
    abstract public function verifyRule();


    /**
     * 启用自动表单审核，返回 false 表示不适用表单审核
     * @return false
     */
    public function useFormVerify()
    {
        return false;
    }

    /**
     * 构建审核表单，返回 null 表示不适用表单审核
     * @param Form $form
     * @param $param
     */
    public function buildForm(Form $form, $param)
    {
        return null;
    }

    /**
     * 后台审核路径
     * @return string
     */
    public function verifyUrl()
    {
        return action($this->verifyRule());
    }

    /**
     * 自动审核成功是否通知
     * @return bool
     */
    public function verifyAutoProcessedNotify()
    {
        return true;
    }

    public function run($param, $title = null, $body = null)
    {
        if (null === $body) {
            $body = [
                '内容' => $title,
            ];
            $shortTitle = Str::substr(HtmlUtil::text2html($title), 0, 100);
        } else {
            $shortTitle = $title;
        }
        $shortTitle = HtmlUtil::text($shortTitle);
        $shortTitle = $this->title() . ($shortTitle ? '(' . $shortTitle . ')' : '');
        if ($this->verifyAutoProcess($param)) {
            if ($this->verifyAutoProcessedNotify()) {
                NotifierProvider::notify($this->name(), '[自动审核]' . $shortTitle, $body, $param);
            }
            return;
        }
        NotifierProvider::notifyNoneLoginOperateProcessUrl(
            $this->name(),
            '[审核]' . $shortTitle,
            $body,
            $this->useFormVerify() ? 'content_verify/' . $this->name() : null,
            $param
        );
    }

    protected function parseRichHtml($content)
    {
        $ret = HtmlUtil::extractTextAndImages($content);
        $images = [];
        $text = $ret['text'];
        foreach ($ret['images'] as $image) {
            $images[] = AssetsUtil::fixFullInJob($image);
        }
        return [
            $text, $images
        ];
    }

    protected function censorRichHtmlSuccess($censorProviderKeyPrefix, $content)
    {
        list($text, $images) = $this->parseRichHtml($content);
        if (!empty($text)) {
            $provider = CensorTextProvider::get(modstart_config($censorProviderKeyPrefix . '_Text', 'default'));
            if ($provider) {
                $ret = $provider->verify($text);
                if (Response::isError($ret)) {
                    return false;
                }
                if (!$ret['data']['pass']) {
                    return false;
                }
            }
        }
        if (!empty($images)) {
            $provider = CensorImageProvider::get(modstart_config($censorProviderKeyPrefix . '_Image', 'default'));
            if ($provider) {
                foreach ($images as $image) {
                    $ret = $provider->verify($image);
                    if (Response::isError($ret)) {
                        return false;
                    }
                    if (!$ret['data']['pass']) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

}
