<?php

namespace ModStart\Session;

/**
 * PHP < 8.0 版本实现（兼容 PHP 5.6 / 7.x）。
 * SessionHandlerInterface 在 PHP < 8.0 的方法无参数/返回类型声明，因此这里同样不写类型。
 */
class SmartRedisSessionHandler extends SmartRedisSessionHandlerBase implements \SessionHandlerInterface
{
    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        $value = $this->getRedis()->get($this->getSessionKey($sessionId));
        return $value === null ? '' : (string)$value;
    }

    public function write($sessionId, $data)
    {
        $ttl = $this->isLogin($data) ? $this->getLoginTtl() : $this->getGuestTtl();
        return (bool)$this->getRedis()->setex($this->getSessionKey($sessionId), $ttl, $data);
    }

    public function destroy($sessionId)
    {
        return $this->getRedis()->del($this->getSessionKey($sessionId)) >= 0;
    }

    public function gc($lifetime)
    {
        return 0;
    }
}
