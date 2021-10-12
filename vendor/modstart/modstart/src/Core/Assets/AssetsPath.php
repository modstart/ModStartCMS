<?php

namespace ModStart\Core\Assets;


interface AssetsPath
{
    /**
     * @param $file string file path
     * @return string file path with hash
     * @example
     *      input  : path/to/static/file.js
     *      output : path/to/static/file.js?v160908023000
     */
    public function getPathWithHash($file);

    /**
     * @param $file string file path
     * @return string server for file path
     * @example
     *      input  : path/to/static/file.js
     *      output : http://cdn.example.com/
     */
    public function getCDN($file);
}

