<?php


namespace ModStart\Core\Events;


class ModStartRequestHandled
{
    public $url;
    public $method;
    public $time;
    public $statusCode;
}
