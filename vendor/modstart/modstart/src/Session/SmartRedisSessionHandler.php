<?php

namespace ModStart\Session;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * 智能 Redis Session Handler（SmartRedisSessionHandler）
 *
 * 根据 Session 内容自动分配不同的有效期（TTL）：
 *   - 已登录用户（会员 memberUserId > 0 / 管理员 _adminUserId > 0）：长有效期（默认 30 天）
 *   - 游客：短有效期（默认 1 小时）
 *
 * 使用方式（在 AppServiceProvider::boot() 中注册一次）：
 *   \ModStart\Session\SmartRedisSessionHandler::register();
 * 或在 .env 中设置 SESSION_DRIVER=smart_redis 后自动生效。
 *
 * 兼容性说明：
 *   - 兼容 Laravel 5.1 / 5.x（PHP 5.6 / 7.x）
 *   - 兼容 Laravel 8 / 9（PHP 8.0+）
 *   - PHP < 8.0 与 PHP >= 8.0 的 SessionHandlerInterface 方法签名不同，
 *     因此按 PHP 版本分发到不同实现文件，本文件为公共逻辑 + 分发入口。
 */
abstract class SmartRedisSessionHandlerBase
{
    /** @var mixed Redis 连接对象（Predis\Client 或 Illuminate\Redis\Connections\Connection） */
    protected $redis;

    /** @var string Session key 前缀 */
    protected $prefix;

    /** @var int 登录用户 Session 有效期（秒） */
    protected $loginTtl;

    /** @var int 游客 Session 有效期（秒） */
    protected $guestTtl;

    /**
     * @param mixed  $redis    Redis 连接对象
     * @param string $prefix   Session key 前缀
     * @param int    $loginTtl 登录用户 TTL（秒）
     * @param int    $guestTtl 游客 TTL（秒）
     */
    public function __construct($redis, $prefix, $loginTtl, $guestTtl)
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->loginTtl = $loginTtl;
        $this->guestTtl = $guestTtl;
    }

    protected function getRedis()
    {
        return $this->redis;
    }

    protected function getPrefix()
    {
        return $this->prefix;
    }

    protected function getLoginTtl()
    {
        return $this->loginTtl;
    }

    protected function getGuestTtl()
    {
        return $this->guestTtl;
    }

    /**
     * 拼接完整的 Redis key（不含连接级 prefix）
     */
    protected function getSessionKey($sessionId)
    {
        return $this->prefix . $sessionId;
    }

    /**
     * 判断 Session 数据是否处于已登录状态。
     *
     * 序列化数据示例：a:8:{s:6:"_token";s:40:"...";...;s:12:"memberUserId";i:5;...}
     * 会员登录：memberUserId > 0；管理员登录：_adminUserId > 0。
     * 这里通过正则匹配序列化格式中的整数值，排除值为 0（未登录）的情况。
     *
     * @param string $data 序列化后的 Session 数据
     * @return bool
     */
    protected function isLogin($data)
    {
        return preg_match('/"(?:memberUserId|_adminUserId)";i:([1-9]\d*)/', (string)$data) === 1;
    }

    /**
     * 注册 smart_redis session 驱动（Laravel 5.1 与 8/9 通用）。
     *
     * @param array $options 可选参数：
     *                       - prefix:     Session key 前缀，默认 '<APP_NAME>:sess:'（如 skillui:sess:）
     *                       - loginTtl:   登录用户有效期（秒），默认 30 天
     *                       - guestTtl:   游客有效期（秒），默认 1 小时
     *                       - connection: 读取 config('database.redis') 中的连接名，默认 'default'（db0）
     */
    public static function register($options = array())
    {
        $prefix = isset($options['prefix']) ? $options['prefix'] : self::defaultPrefix();
        $loginTtl = isset($options['loginTtl']) ? intval($options['loginTtl']) : 30 * 24 * 60 * 60; // 30 天（秒）
        $guestTtl = isset($options['guestTtl']) ? intval($options['guestTtl']) : 60 * 60;         // 1 小时（秒）
        $connection = isset($options['connection']) ? $options['connection'] : 'default';         // 默认 db0

        Session::extend('smart_redis', function ($app) use ($prefix, $loginTtl, $guestTtl, $connection) {
            return new static(self::createRedisClient($connection), $prefix, $loginTtl, $guestTtl);
        });
    }

    /**
     * 默认 Session key 前缀：<APP_NAME>:sess:
     * 例如 APP_NAME=skillui 时前缀为 skillui:sess:
     */
    protected static function defaultPrefix()
    {
        $name = config('env.APP_NAME');
        if (empty($name)) {
            $name = env('APP_NAME');
        }
        if (empty($name)) {
            $name = config('app.name');
        }
        if (empty($name)) {
            $name = 'laravel';
        }
        return Str::slug($name, '_') . ':sess:';
    }

    /**
     * 创建 Redis 连接。
     *
     * 直接使用 Predis\Client（modstart 依赖 predis/predis），并显式关闭连接级前缀，
     * 使最终 key 完全由 handler 的 prefix 控制（<APP_NAME>:sess:<sessionId>），
     * 不受 Laravel database.redis.options.prefix / config 缓存影响。
     *
     * @param string $connection config('database.redis') 中的连接名
     * @return \Predis\Client
     */
    protected static function createRedisClient($connection = 'default')
    {
        $config = config('database.redis.' . $connection, array());
        $parameters = array(
            'scheme' => 'tcp',
            'host' => isset($config['host']) ? $config['host'] : '127.0.0.1',
            'port' => isset($config['port']) ? intval($config['port']) : 6379,
            'database' => isset($config['database']) ? intval($config['database']) : 0,
        );
        if (!empty($config['password'])) {
            $parameters['password'] = $config['password'];
        }
        return new \Predis\Client($parameters, array(
            'prefix' => '', // 关闭连接级前缀
        ));
    }
}

/*
 * SessionHandlerInterface 方法签名随 PHP 版本变化：
 *   - PHP < 8.0：方法无参数类型 / 返回类型声明（5.x 尚未引入返回类型特性）
 *   - PHP >= 8.0：方法带 string / bool 等类型声明，且 PHP 5.x 语法无法解析
 * 因此按 PHP 版本分发到不同实现文件，保证同一套代码在 PHP 5.6 与 PHP 8.x 均可运行。
 */
if (PHP_VERSION_ID >= 80000) {
    require __DIR__ . '/SmartRedisSessionHandler.php8.php';
} else {
    require __DIR__ . '/SmartRedisSessionHandler.php5.php';
}
