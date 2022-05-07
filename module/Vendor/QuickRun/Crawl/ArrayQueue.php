<?php

namespace Module\Vendor\QuickRun\Crawl;

class ArrayQueue implements BaseQueue
{
    private $data = [];

    function prepend($handler, $param = [], $id = null)
    {
        array_unshift($this->data, [
            'id' => $id,
            'handler' => $handler,
            'param' => $param
        ]);
    }


    function append($handler, $param = [], $id = null)
    {
        array_push($this->data, [
            'id' => $id,
            'handler' => $handler,
            'param' => $param
        ]);
    }

    function exists($id)
    {
        foreach ($this->data as $item) {
            if ($item['id'] == $id) {
                return true;
            }
        }
        return false;
    }

    function size()
    {
        return count($this->data);
    }

    function poll()
    {
        return array_shift($this->data);
    }

}
