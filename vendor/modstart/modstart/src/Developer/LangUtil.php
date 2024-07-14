<?php


namespace ModStart\Developer;


use ModStart\Core\Util\SerializeUtil;

class LangUtil
{
    /**
     * @deprecated delete at 2025-01-14
     */
    public static function extractFileLangScripts($file)
    {
        $file = base_path($file);
        if (!file_exists($file)) {
            return '';
        }
        $content = file_get_contents($file);
        preg_match_all('/L\\((([^()]*|\\([^()]*\\))*)\\)/', $content, $mat);
        if (!empty($mat[1])) {
            $langs = [];
            foreach ($mat[0] as $item) {
                if (preg_match('/^L\\([\'|"](.*?)[\'|"].*?\\)/', $item, $mat1)) {
                    $langs[$mat1[1]] = L($mat1[1]);
                }
            }
            ksort($langs);
            return "\n{!! \ModStart\ModStart::lang(" . SerializeUtil::jsonEncodePretty(array_keys($langs)) . ") !!}";
        }
        return '';
    }

    /**
     * @deprecated delete at 2025-01-14
     */
    public static function langScriptPrepare($langs)
    {
        $script = [];
        $script[] = "\n(function(){";
        $script[] = "  window.lang = window.lang||{};";
        foreach ($langs as $l) {
            $script[] = "  window.lang[" . SerializeUtil::jsonEncode($l) . "]=" . SerializeUtil::jsonEncode(L($l)) . ";";
        }
        $script[] = "})();";
        return join("\n", $script);
    }
}
