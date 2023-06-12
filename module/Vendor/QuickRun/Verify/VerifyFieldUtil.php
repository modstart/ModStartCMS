<?php


namespace Module\Vendor\QuickRun\Verify;

use ModStart\Support\Manager\FieldManager;
use Module\Vendor\QuickRun\Verify\Field\LayoutHtmlVerifyFoot;

class VerifyFieldUtil
{
    public static function register()
    {
        FieldManager::extend('layoutHtmlVerifyFoot', LayoutHtmlVerifyFoot::class);
    }

}
