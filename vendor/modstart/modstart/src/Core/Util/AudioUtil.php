<?php

namespace ModStart\Core\Util;


use ModStart\Core\Util\Support\MP3File;

class AudioUtil
{
    public static function mp3Duration($mp3File)
    {
        $file = new MP3File($mp3File);
        return $file->getDuration();
    }
}
