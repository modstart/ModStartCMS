<?php


namespace Module\Vendor\Provider\Notifier;


use Module\Vendor\Util\CacheUtil;
use Module\Vendor\Util\NoneLoginOperateUtil;

class NotifierProvider
{
    /**
     * @var array
     */
    private static $list = [];

    public static function register($provider)
    {
        self::$list[] = $provider;
    }

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

            foreach (self::$list as $k => $v) {
                if ($v instanceof \Closure) {
                    self::$list[$k] = call_user_func($v);
                } else if (is_string($v)) {
                    self::$list[$k] = app($v);
                }
            }
            $instances = array_merge($instances, self::$list);

        }
        return $instances;
    }

    /**
     * 发送消息通知
     * @param $biz string 业务标识
     * @param $title string 标题
     * @param $content array|string 内容
     * @param $param array 参数
     */
    public static function notify($biz, $title, $content, $param = [])
    {
        foreach (self::all() as $instance) {
            $instance->notify($biz, $title, $content, $param);
        }
    }

    /**
     * 发送消息通知（指定时间间隔）
     * @param $period int 时间间隔（秒）
     * @param $biz string 业务标识
     * @param $title string 标题
     * @param $content array|string 内容
     * @param $param array
     */
    public static function notifyPeriod($period, $biz, $title, $content, $param = [])
    {
        CacheUtil::remember("Notifier:NotifyPeriod:{$biz}:{$period}",
            $period,
            function () use ($biz, $title, $content, $param) {
                self::notify($biz, $title, $content, $param);
                return time();
            }
        );
    }

    public static function notifyNoneLoginOperateProcessUrl($biz, $title, $content, $processUrlPath, $param = [])
    {
        $systemParam = [
            'domainUrl' => null,
            'viewUrl' => null,
        ];
        foreach ($systemParam as $k => $v) {
            if (isset($param[$k])) {
                $systemParam[$k] = $param[$k];
                unset($param[$k]);
            }
        }
        if ($processUrlPath) {
            $processUrl = NoneLoginOperateUtil::generateUrl($processUrlPath, $param, $systemParam['domainUrl']);
            $systemParam['processUrl'] = $processUrl;
        }
        self::notify($biz, $title, $content, array_merge($param, $systemParam));
    }
}
