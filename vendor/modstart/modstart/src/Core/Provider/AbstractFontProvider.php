<?php


namespace ModStart\Core\Provider;


abstract class AbstractFontProvider
{
    public abstract function name();

    public abstract function title();

    public abstract function path();
}
