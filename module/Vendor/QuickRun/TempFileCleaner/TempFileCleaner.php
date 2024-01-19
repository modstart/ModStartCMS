<?php


namespace Module\Vendor\QuickRun\TempFileCleaner;


use ModStart\Core\Util\FileUtil;

class TempFileCleaner
{
    private $paths = [];
    private $folders = [];

    public function add($p)
    {
        if (is_array($p)) {
            $this->paths = array_merge($this->paths, $p);
        } else {
            $this->paths[] = $p;
        }
    }

    public function addFolder($p)
    {
        if (is_array($p)) {
            $this->folders = array_merge($this->folders, $p);
        } else {
            $this->folders[] = $p;
        }
    }

    public function clean()
    {
        foreach ($this->paths as $p) {
            if (!file_exists($p)) {
                continue;
            }
            FileUtil::safeCleanLocalTemp($p);
        }
        foreach ($this->folders as $p) {
            if (!file_exists($p)) {
                continue;
            }
            FileUtil::rm($p, true);
        }
    }

}
