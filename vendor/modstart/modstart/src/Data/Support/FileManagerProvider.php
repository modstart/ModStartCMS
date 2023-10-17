<?php


namespace ModStart\Data\Support;


use ModStart\Core\Provider\ProviderTrait;

/**
 * @method static AbstractFileManager[] listAll();
 */
class FileManagerProvider
{
    use ProviderTrait;

    private static $list = [];
}
