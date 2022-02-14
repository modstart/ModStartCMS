<?php


namespace ModStart\Core\Util;

class VersionUtil
{
    /**
     * 对比两个版本号
     *
     * @param $version string 版本号
     * @param $targetVersionWithOperator string 版本号规则，如 >=1.0.0, ==1.0.0, 1.0.0 <1.0.0 等
     * @return bool
     */
    public static function match($version, $targetVersionWithOperator)
    {
        if ('*' == $targetVersionWithOperator) {
            return true;
        }
        $support = ['>=', '<=', '==', '>', '<'];
        $operator = '==';
        foreach ($support as $item) {
            if (starts_with($targetVersionWithOperator, $item)) {
                $operator = $item;
                $targetVersionWithOperator = substr($targetVersionWithOperator, strlen($item));
                break;
            }
        }
        return version_compare($version, $targetVersionWithOperator, $operator);
    }

    /**
     * 解析一个模块版本依赖
     *
     * @param $nameVersion string 规则 Name:1.0.0, Name:>=1.0.0
     * @return array
     */
    public static function parse($nameVersion)
    {
        $pcs = explode(':', $nameVersion);
        if (count($pcs) == 1) {
            return [$pcs[0], '*'];
        }
        return [$pcs[0], $pcs[1]];
    }

    /**
     * 判断字符串是否为版本号
     * @param $versionString
     * @return bool
     * @since 1.5.0
     */
    public static function isVersion($versionString)
    {
        return preg_match('/^\\d+\\.\\d+\\.\\d+$/i', $versionString)
            || preg_match('/^\\d+\\.\\d+\\.\\d+\\-(alpha|beta|release)$/i', $versionString)
            || preg_match('/^\\d+\\.\\d+\\.\\d+\\-(alpha|beta|release)\\-\\d+$/i', $versionString);
    }
}
