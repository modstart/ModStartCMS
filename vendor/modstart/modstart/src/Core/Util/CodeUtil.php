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

    public static function getFullClassNameForContent($content)
    {
        $namespace = self::getClassNamespaceForContent($content);
        if (empty($namespace)) {
            return null;
        }
        $cls = self::getClassNameForContent($content);
        if (empty($cls)) {
            return null;
        }
        return "$namespace\\$cls";
    }

    public static function getClassNameForContent($content)
    {
        $classes = array();
        $tokens = token_get_all($content);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {

                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }
        return $classes[0];
    }

    public static function getClassNamespaceForContent($content)
    {
        $tokens = token_get_all($content);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        if (!$namespace_ok) {
            return null;
        } else {
            return $namespace;
        }
    }

    public static function htmlBeauty($html)
    {
        $tidy = \tidy_parse_string($html, array(
            'indent-spaces' => 4,
            'indent' => true,
            'output-xhtml' => true,
            'show-body-only' => true,
            'wrap' => 200,
        ), 'UTF8');
        $tidy->cleanRepair();
        return (string)$tidy;
    }
}
