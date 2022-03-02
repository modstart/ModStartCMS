<?php


namespace Module\Vendor\Provider\Notifier;


use Module\Vendor\Util\NoneLoginOperateUtil;

class NotifierProvider
{
    /**
     * @return AbstractNotifierProvider[]
     */
    public static function all()
    {
        static $instances = null;
        if (null === $instances) {
            $drivers = config('NotifierProviders');
            if (empty($drivers)) {
                $drivers = [
                    DefaultNotifierProvider::class
                ];
            }
            $instances = array_map(function ($driver) {
                return app($driver);
            }, array_unique($drivers));
        }
        return $instances;
    }

    /**
     * 发送消息通知
     * @param $biz
     * @param $title
     * @param $content
     * @param array $param
     */
    public static function notify($biz, $title, $content, $param = [])
    {
        foreach (self::all() as $instance) {
            $instance->notify($biz, $title, $content, $param);
        }
    }

    public static function notifyProcess($biz, $title, $content, $processUrl)
    {
        self::notify($biz, $title, $content, [
            'processUrl' => $processUrl,
        ]);
    }

    public static function notifyNoneLoginOperateProcessUrl($biz, $title, $content, $processUrlPath, $processUrlParam = [])
    {
        $processUrl = NoneLoginOperateUtil::generateUrl($processUrlPath, $processUrlParam);
        self::notifyProcess($biz, $title, $content, $processUrl);
    }
}
