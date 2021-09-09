<?php
/**
 * This file is part of ninja-mutex.
 *
 * (C) Kamil Dziedzic <arvenil@klecza.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NinjaMutex\Lock;

use Memcache;

/**
 * Lock implementor using Memcache
 *
 * @author Kamil Dziedzic <arvenil@klecza.pl>
 */
class MemcacheLock extends LockAbstract implements LockExpirationInterface
{
    /**
     * Maximum expiration time in seconds (30 days)
     * http://php.net/manual/en/memcache.add.php
     */
    const MAX_EXPIRATION = 2592000;

    /**
     * Memcache connection
     *
     * @var Memcache
     */
    protected $memcache;

    /**
     * @var int Expiration time of the lock in seconds
     */
    protected $expiration = 0;

    /**
     * @param Memcache $memcache
     */
    public function __construct(Memcache $memcache)
    {
        parent::__construct();

        $this->memcache = $memcache;
    }

    /**
     * @param int $expiration Expiration time of the lock in seconds. If it's equal to zero (default), the lock will never expire.
     *                        Max 2592000s (30 days), if greater it will be capped to 2592000 without throwing an error.
     *                        WARNING: Using value higher than 0 may lead to race conditions. If you set too low expiration time
     *                        e.g. 30s and critical section will run for 31s another process will gain lock at the same time,
     *                        leading to unpredicted behaviour. Use with caution.
     */
    public function setExpiration($expiration)
    {
        if ($expiration > static::MAX_EXPIRATION) {
            $expiration = static::MAX_EXPIRATION;
        }
        $this->expiration = $expiration;
    }

    /**
     * Clear lock without releasing it
     * Do not use this method unless you know what you do
     *
     * @param  string $name name of lock
     * @return bool
     */
    public function clearLock($name)
    {
        if (!isset($this->locks[$name])) {
            return false;
        }

        unset($this->locks[$name]);
        return true;
    }

    /**
     * @param  string $name name of lock
     * @param  bool   $blocking
     * @return bool
     */
    protected function getLock($name, $blocking)
    {
        if (!$this->memcache->add($name, serialize($this->getLockInformation()), 0, $this->expiration)) {
            return false;
        }

        return true;
    }

    /**
     * Release lock
     *
     * @param  string $name name of lock
     * @return bool
     */
    public function releaseLock($name)
    {
        if (isset($this->locks[$name]) && $this->memcache->delete($name)) {
            unset($this->locks[$name]);

            return true;
        }

        return false;
    }

    /**
     * Check if lock is locked
     *
     * @param  string $name name of lock
     * @return bool
     */
    public function isLocked($name)
    {
        return false !== $this->memcache->get($name);
    }
}
