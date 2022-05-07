<?php

namespace Module\Vendor\QuickRun\Crawl;

interface BaseQueue
{
    function prepend($handler, $param = [], $id = null);

    function append($handler, $param = [], $id = null);

    function exists($id);

    function size();

    function poll();
}
