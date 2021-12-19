<?php


namespace ModStart\Core\Util;


class CodeUtil
{
    public static function cssRemoveComments($code)
    {
        $code = str_replace("/*", "__COMSTART", $code);
        $code = str_replace("*/", "COMEND__", $code);
        $code = preg_replace("/__COMSTART[\s\S]*?COMEND__/s", "", $code);
        return $code;
    }

    public static function jsRemoveComments($code)
    {
        $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/';
        $code = preg_replace($pattern, '', $code);
        return $code;
    }

    public static function phpRemoveComments($code)
    {
        $commentTokens = array(T_COMMENT);
        if (defined('T_DOC_COMMENT')) {
            $commentTokens[] = T_DOC_COMMENT; // PHP 5
        }
        if (defined('T_ML_COMMENT')) {
            $commentTokens[] = T_ML_COMMENT;  // PHP 4
        }
        $codeNew = [];
        $tokens = token_get_all($code);
        $prevEmpty = false;
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    continue;
                }
                $token = $token[1];
            }
            $codeNew[] = $token;
        }
        return join('', $codeNew);
    }

    public static function phpVarExport($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                if (strpos($var, ':RAW:') === 0) {
                    return substr($var, strlen(':RAW:'));
                }
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : self::phpVarExport($key) . " => ")
                        . self::phpVarExport($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "true" : "false";
            default:
                return var_export($var, true);
        }
    }

    public static function phpVarExportReturnFile($var)
    {
        $content = self::phpVarExport($var);
        return "<?php\nreturn $content;";
    }
}