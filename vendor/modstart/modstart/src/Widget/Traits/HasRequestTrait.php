<?php

namespace ModStart\Widget\Traits;

trait HasRequestTrait
{
    abstract public function request();

    abstract public function permit();
}
