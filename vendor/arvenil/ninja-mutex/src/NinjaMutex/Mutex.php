<?php
/**
 * This file is part of ninja-mutex.
 *
 * (C) Kamil Dziedzic <arvenil@klecza.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NinjaMutex;

use NinjaMutex\Lock\LockInterface;

/**
 * Mutex
 *
 * @author Kamil Dziedzic <arvenil@klecza.pl>
 */
class Mutex
{
    /**
     * Lock implementor
     *
     * @var LockInterface
     */
    protected $lockImplementor;

    /**
     * Name of lock
     *
     * @var string
     */
    protected $name;

    /**
     * Lock counter to protect against recursive deadlock
     *
     * @var integer
     */
    protected $counter = 0;

    /**
     * @param string        $name
     * @param LockInterface $lockImplementor
     */
    public function __construct($name, LockInterface $lockImplementor)
    {
        $this->name = $name;
        $this->lockImplementor = $lockImplementor;
    }

    /**
     * @param  int|null $timeout
     * @return bool
     */
    public function acquireLock($timeout = null)
    {
        if ($this->counter > 0 ||
            $this->lockImplementor->acquireLock($this->name, $timeout)) {
            $this->counter++;

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function releaseLock()
    {
        if ($this->counter > 0) {
            $this->counter--;
            if ($this->counter > 0 ||
                $this->lockImplementor->releaseLock($this->name)) {
                return true;
            }
            $this->counter++;
        }

        return false;
    }

    /**
     * Try to release any obtained locks when object is destroyed
     *
     * This is a safe guard for cases when your php script dies unexpectedly.
     * It's not guaranteed it will work either.
     *
     * You should not depend on __destruct() to release your locks,
     * instead release them with `$released = $this->releaseLock()`A
     * and check `$released` if lock was properly released
     */
    public function __destruct()
    {
        while ($this->isAcquired()) {
            $released = $this->releaseLock();
            if (!$released) {
                throw new UnrecoverableMutexException(sprintf(
                    'Cannot release lock in Mutex __destruct(): %s',
                    $this->name
                ));
            }
        }
    }

    /**
     * Check if Mutex is acquired
     *
     * @return bool
     */
    public function isAcquired()
    {
        return $this->counter > 0;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->lockImplementor->isLocked($this->name);
    }
}
