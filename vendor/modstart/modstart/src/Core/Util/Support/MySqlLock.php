<?php

namespace ModStart\Core\Util\Support;

use Illuminate\Support\Facades\DB;
use NinjaMutex\Lock\LockAbstract;
use \PDO;

class MySqlLock extends LockAbstract
{
    /**
     * MySql connections
     *
     * @var PDO[]
     */
    protected $pdo = array();

    public function __clone()
    {
        parent::__clone();
        $this->pdo = array();
    }


    /**
     * Acquire lock
     *
     * @param string $name name of lock
     * @param null|int $timeout 1. null if you want blocking lock
     *                           2. 0 if you want just lock and go
     *                           3. $timeout > 0 if you want to wait for lock some time (in milliseconds)
     * @return bool
     */
    public function acquireLock($name, $timeout = null)
    {
        if (!$this->setupPDO($name)) {
            return false;
        }

        if ($timeout > 0) {
            return 1 && $this->pdo[$name]->query(
                    sprintf(
                        'SELECT GET_LOCK("%s", %d)',
                        $name,
                        $timeout / 1000
                    ),
                    PDO::FETCH_COLUMN,
                    0
                )->fetch();
        }
        return parent::acquireLock($name, $timeout);
    }

    /**
     * @param string $name
     * @param bool $blocking
     * @return bool
     */
    protected function getLock($name, $blocking)
    {
        return 1 && $this->pdo[$name]->query(
                sprintf(
                    'SELECT GET_LOCK("%s", %d)',
                    $name,
                    1
                ),
                PDO::FETCH_COLUMN,
                0
            )->fetch();
    }

    /**
     * Release lock
     *
     * @param string $name name of lock
     * @return bool
     */
    public function releaseLock($name)
    {
        if (!$this->setupPDO($name)) {
            return false;
        }

        $released = (bool)$this->pdo[$name]->query(
            sprintf(
                'SELECT RELEASE_LOCK("%s")',
                $name
            ),
            PDO::FETCH_COLUMN,
            0
        )->fetch();

        if (!$released) {
            return false;
        }

        unset($this->pdo[$name]);
        unset($this->locks[$name]);

        return true;
    }

    /**
     * Check if lock is locked
     *
     * @param string $name name of lock
     * @return bool
     */
    public function isLocked($name)
    {
        if (empty($this->pdo) && !$this->setupPDO($name)) {
            return false;
        }

        return !current($this->pdo)->query(
            sprintf(
                'SELECT IS_FREE_LOCK("%s")',
                $name
            ),
            PDO::FETCH_COLUMN,
            0
        )->fetch();
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function setupPDO($name)
    {
        if (isset($this->pdo[$name])) {
            return true;
        }
        $this->pdo[$name] = DB::connection('mysql')->getPdo();
        return true;
    }

    public function __destruct()
    {
        parent::__destruct();

        foreach ($this->pdo as $name => $pdo) {
            unset($this->pdo[$name]);
        }
    }
}
