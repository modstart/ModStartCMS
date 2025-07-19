<?php

namespace ModStart\Core\Type;

use Illuminate\Support\Str;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ConstantUtil;
use ModStart\Core\Util\SerializeUtil;

class TypeUtil
{
    public static function dumpJsFile($types)
    {
        $constants = [];
        foreach ($types as $type) {
            $constants[class_basename($type)] = TypeUtil::dump($type);
        }
        $content = [];
        $content [] = "// This file is created by " . Request::currentPageUrl() . "\n";
        foreach ($constants as $name => $json) {
            $content[] = "export const $name = " . SerializeUtil::jsonEncodePretty($json) . ";";
        }
        return Response::raw(join("\n", $content), ['Content-Type' => 'text/plain']);
    }

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

    public static function filter($typeCls, $values)
    {
        $list = $typeCls::getList();
        $filter = [];
        foreach ($list as $k => $v) {
            if (in_array($k, $values)) {
                $filter[$k] = $v;
            }
        }
        return $filter;
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
            'completed' => 'success',
            'finish' => 'success',
            'success' => 'success',
            'verified' => 'success',
            'processed' => 'success',
            'valid' => 'success',
            'online' => 'success',
            'payed' => 'success',
            'enable' => 'success',
            'pass' => 'success',

            'wait_pay' => 'warning',
            'danger' => 'warning',
            'running' => 'warning',
            'converting' => 'warning',
            'wait_process' => 'warning',
            'verifying' => 'warning',
            'invalid' => 'warning',
            'sending' => 'warning',

            'fail' => 'danger',
            'error' => 'danger',
            'wait_verify' => 'danger',
            'reject' => 'danger',
            'unread' => 'danger',
            'refund' => 'danger',

            'wait' => 'muted',
            'canceled' => 'muted',
            'disable' => 'muted',
            'ignore' => 'muted',
            'expired' => 'muted',
            'read' => 'muted',
            'deleted' => 'muted',

            'info' => 'primary',
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
