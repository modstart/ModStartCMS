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

/**
 * Lock implementor
 *
 * @author Kamil Dziedzic <arvenil@klecza.pl>
 */
interface LockExpirationInterface
{
    /**
     * @param int $expiration Expiration time of the lock in seconds.
     */
    public function setExpiration($expiration);

    /**
     * @param  string $name
     * @return bool
     */
    public function clearLock($name);
}
