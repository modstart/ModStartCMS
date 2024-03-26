<?php

namespace Module\Vendor\Log;

trait LoggerTrait
{
    private static $loggerFileName = null;

    public static function loggerName($name = null)
    {
        if (is_null($name)) {
            if (is_null(self::$loggerFileName)) {
                self::$loggerFileName = class_basename(__CLASS__);
            }
            return self::$loggerFileName;
        }
        self::$loggerFileName = $name;
    }

    public static function loggerInfo($label, $msg = null)
    {
        return Logger::info(self::loggerName(), $label, $msg);
    }

    public static function loggerError($label, $msg = null)
    {
        return Logger::error(self::loggerName(), $label, $msg);
    }
}
