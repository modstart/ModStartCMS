<?php


namespace Module\Vendor\Provider\Notifier;


use Illuminate\Support\Facades\Log;
use ModStart\Core\Util\SerializeUtil;

class DefaultNotifierProvider extends AbstractNotifierProvider
{
    public function name()
    {
        return 'Default';
    }

    public function title()
    {
        return '默认日志';
    }

    public function notify($biz, $title, $content, $param = [])
    {
        Log::info(sprintf('Vendor.DefaultNotifierProvider - %s - %s - %s', $biz, $title, SerializeUtil::jsonEncode($content, JSON_UNESCAPED_UNICODE)));
    }
}
