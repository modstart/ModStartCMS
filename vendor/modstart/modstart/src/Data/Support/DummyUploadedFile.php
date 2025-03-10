<?php

namespace ModStart\Data\Support;

use ModStart\Core\Util\FileUtil;

class DummyUploadedFile
{
    private $path;

    public static function create($content)
    {
        $f = new static();
        $f->path = FileUtil::generateLocalTempPath();
        file_put_contents($f->path, $content);
        return $f;
    }

    public function __destruct()
    {
        if (file_exists($this->path)) {
            @unlink($this->path);
        }
    }


    public function getRealPath()
    {
        return $this->path;
    }

    public function getPathname()
    {
        return $this->path;
    }
}
