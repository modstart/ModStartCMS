<?php

namespace ModStart\Core\Assets;


interface AssetsPath
{
    
    public function getPathWithHash($file);

    
    public function getCDN($file);
}

