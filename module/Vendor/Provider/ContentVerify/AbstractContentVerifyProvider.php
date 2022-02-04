<?php


namespace Module\Vendor\Provider\ContentVerify;


use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Form\Form;
use Module\Vendor\Provider\CensorImage\CensorImageProvider;
use Module\Vendor\Provider\CensorText\CensorTextProvider;
use Module\Vendor\Provider\Notifier\NotifierProvider;

abstract class AbstractContentVerifyProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function buildForm(Form $form, $param);

    abstract public function autoProcessed($param);

    public function run($param, $title = null, $body = null)
    {
        if (null === $body) {
            $body = [
                '标题' => $title,
            ];
        }
        if ($this->autoProcessed($param)) {
            return;
        }
        NotifierProvider::notifyNoneLoginOperateProcessUrl(
            $this->name(),
            '[审核]' . $this->title() . ($title ? '(' . $title . ')' : ''),
            $body,
            'content_verify/' . $this->name(), $param
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
            $ret = $provider->verify($text);
            if (Response::isError($ret)) {
                return false;
            }
            if (!$ret['data']['pass']) {
                return false;
            }
        }
        if (!empty($images)) {
            $provider = CensorImageProvider::get(modstart_config($censorProviderKeyPrefix . '_Image', 'default'));
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
        return true;
    }

}