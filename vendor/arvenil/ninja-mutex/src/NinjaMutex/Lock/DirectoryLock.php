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
 * Lock implementor using mkdir
 *
 * @author Jan Voracek <jan@voracek.net>
 */
class DirectoryLock extends LockAbstract
{
    protected $dirname;

    /**
     * @param string $dirname
     */
    public function __construct($dirname)
    {
        parent::__construct();

        $this->dirname = $dirname;
    }

    /**
     * @param  string $name
     * @param  bool   $blocking
     * @return bool
     */
    protected function getLock($name, $blocking)
    {
        while (!@mkdir($this->getDirectoryPath($name))) {
            if (!$blocking) {
                return false;
            }

            usleep(rand(5000, 20000));
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
        if (isset($this->locks[$name])) {
            rmdir($this->getDirectoryPath($name));
            unset($this->locks[$name]);

            return true;
        }

        return false;
    }

    /**
     * @param  string $name
     * @return string
     */
    protected function getDirectoryPath($name)
    {
        return $this->dirname . DIRECTORY_SEPARATOR . $name . '.lock';
    }

    /**
     * Check if lock is locked
     *
     * @param  string $name name of lock
     * @return bool
     */
    public function isLocked($name)
    {
        if ($this->acquireLock($name, false)) {
            return !$this->releaseLock($name);
        }

        return true;
    }
}
