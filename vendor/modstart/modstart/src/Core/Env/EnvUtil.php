<?php

namespace ModStart\Core\Env;

/**
 * Class EnvUtil
 * @package ModStart\Core\Env
 * @deprecated delete at 2024-04-24 use Util\EnvUtil instead
 */
class EnvUtil
{
    public static function parse($file)
    {
        $all = [];
        if (file_exists($file)) {
            foreach (explode("\n", file_get_contents($file)) as $line) {
                if ($line = trim($line)) {
                    if (substr($line, 0, 1) === '#') {
                        continue;
                    }
                    $pcs = explode('=', $line);
                    $k = trim($pcs[0]);
                    array_shift($pcs);
                    $v = trim(join('=', $pcs));
                    switch (strtolower($v)) {
                        case 'true':
                        case '(true)':
                            $v = true;
                            break;
                        case 'false':
                        case '(false)':
                            $v = false;
                            break;
                        case 'empty':
                        case '(empty)':
                            $v = '';
                            break;
                        case 'null':
                        case '(null)':
                            $v = null;
                            break;
                    }
                    $all[$k] = $v;
                }
            }
        }
        return $all;
    }

    public static function all($file = null)
    {
        global $__msConfig;
        if (!empty($__msConfig)) {
            return $__msConfig;
        }
        if (null === $file) {
            $file = base_path('.env');
        }
        return self::parse($file);
    }
}
