<?php

namespace ModStart\Session;

/**
 * PHP >= 8.0 版本实现（兼容 PHP 8.0 / 8.1 / 8.2+，Laravel 8 / 9）。
 * SessionHandlerInterface 在 PHP >= 8.0 的方法带类型声明，这里使用完整签名实现。
 */
class SmartRedisSessionHandler extends SmartRedisSessionHandlerBase implements \SessionHandlerInterface
{
    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $sessionId): string|false
    {
        $value = $this->getRedis()->get($this->getSessionKey($sessionId));
        return $value === null ? '' : (string)$value;
    }

    public function write(string $sessionId, string $data): bool
    {
        $ttl = $this->isLogin($data) ? $this->getLoginTtl() : $this->getGuestTtl();
        return (bool)$this->getRedis()->setex($this->getSessionKey($sessionId), $ttl, $data);
    }

    public function destroy(string $sessionId): bool
    {
        return $this->getRedis()->del($this->getSessionKey($sessionId)) >= 0;
    }

    public function gc(int $maxLifetime): int|false
    {
        return 0;
    }
}
