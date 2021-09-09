<?php


namespace EasyWeChat\Kernel\Cache;


use Doctrine\Common\Cache\CacheProvider;
use Psr\SimpleCache\CacheInterface;

class CacheProviderAdapter implements CacheInterface
{
    /** @var CacheProvider */
    private $driver;

    /**
     * CacheProviderAdapter constructor.
     * @param CacheProvider $driver
     */
    public function __construct(CacheProvider $driver)
    {
        $this->driver = $driver;
    }


    public function get($key, $default = null)
    {
        $value = $this->driver->fetch($key);
        if (null == $value) {
            return $default;
        }
        return $value;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->driver->save($key, $value, $ttl);
    }

    public function delete($key)
    {
        $this->driver->delete($key);
    }

    public function clear()
    {
        $this->driver->flushAll();
    }

    public function getMultiple($keys, $default = null)
    {
        $value = $this->driver->fetchMultiple($keys);
        if (null == $value) {
            return $default;
        }
        return $value;
    }

    public function setMultiple($values, $ttl = null)
    {
        $this->driver->saveMultiple($values, $ttl);
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->driver->delete($key);
        }
    }

    public function has($key)
    {
        return $this->driver->contains($key);
    }

}