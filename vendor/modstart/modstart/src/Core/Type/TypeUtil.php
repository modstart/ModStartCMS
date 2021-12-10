<?php

namespace ModStart\Core\Type;

use Illuminate\Support\Str;
use ModStart\Core\Util\ConstantUtil;

class TypeUtil
{
    public static function name($typeCls, $value)
    {
        $list = $typeCls::getList();
        foreach ($list as $k => $v) {
            if ($k == $value) {
                return $v;
            }
        }
        return null;
    }

    public static function dump($cls)
    {
        $keys = ConstantUtil::dump($cls);
        $map = $cls::getList();
        foreach ($keys as $key => $value) {
            $values[$key]['key'] = $key;
            $values[$key]['value'] = $value;
            $values[$key]['name'] = (isset($map[$value]) ? $map[$value] : null);
        }
        return $values;
    }

    public static function colorGuessMap($typeClass)
    {
        static $cache = [];
        if (isset($cache[$typeClass])) {
            return $cache[$typeClass];
        }
        $map = [];
        $guesses = [
            'fail' => 'danger',
            'error' => 'danger',
            'wait_verify' => 'danger',
            'wait_pay' => 'warning',
            'danger' => 'warning',
            'running' => 'warning',
            'success' => 'success',
            'verified' => 'success',
            'pass' => 'success',
            'reject' => 'danger',
            'wait' => 'muted',
            'canceled' => 'muted',
            'completed' => 'success',
            'finish' => 'success',
            'converting' => 'warning',
            'sending' => 'warning',
            'ignore' => 'muted',
            'expired' => 'muted',
            'info' => 'primary',
            'unread' => 'danger',
            'read' => 'muted',
            'processed' => 'success',
            'verifying' => 'warning',
            'online' => 'success',
            'payed' => 'success',
            'wait_process' => 'warning',
            'deleted' => 'muted',
        ];
        foreach (ConstantUtil::dump($typeClass) as $k => $v) {
            $k = strtolower($k);
            foreach ($guesses as $guessKey => $guessValue) {
                if (Str::contains($k, $guessKey)) {
                    $map[$v] = $guessValue;
                    break;
                }
            }
        }
        $cache[$typeClass] = $map;
        return $map;
    }
}
