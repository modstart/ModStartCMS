<?php

namespace ModStart\Widget\Traits;

use ModStart\Core\Util\ReUtil;
use ModStart\ModStart;

trait HasVueFileTrait
{
    public function content()
    {
        if (method_exists($this, 'getVuePath')) {
            $filePath = $this->getVuePath();
        } else {
            $reflector = new \ReflectionClass(get_class($this));
            $filePath = $reflector->getFileName();
            $filePath = preg_replace('/\.php$/', '.vue', $filePath);
        }

        $vueTemplate = '<div class="ub-alert danger">Vue file not found: ' . $filePath . '</div>';
        $vueScript = 'export default {}';
        $vueStyle = '';

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $script = trim(ReUtil::group1('/<script>([\s\S]+)<\/script>/', $content));
            if (!empty($script)) {
                $vueScript = $script;
            }
            $template = trim(ReUtil::group1('/<template>([\s\S]+)<\/template>/', $content));
            if (empty($template)) {
                $vueTemplate = '<div class="ub-alert danger">Vue template parse fail: ' . $filePath . '</div>';
            } else {
                $vueTemplate = $template;
            }
            $vueStyle = trim(ReUtil::group1('/<style>([\s\S]+)<\/style>/', $content));
        }

        $vueScript = preg_replace('/export default/', 'let _widget = ', $vueScript) . ';';
        $vueInitParam = new \stdClass();
        if (method_exists($this, 'initParam')) {
            $vueInitParam = call_user_func([$this, 'initParam']);
        }

        $setting = [
            'importVueBase' => true,
        ];
        if (property_exists($this, '_setting')) {
            $setting = array_merge($setting, $this->_setting);
        }
        if ($setting['importVueBase']) {
            ModStart::js([
                'asset/vendor/vue.js',
                'asset/vendor/element-ui/index.js',
            ]);
        }

        if (method_exists($this, 'contentRenderBefore')) {
            call_user_func([$this, 'contentRenderBefore']);
        }

        ModStart::script(join('', [
            "(function(){",
            "Vue.use(ELEMENT, {size: 'mini', zIndex: 3000});",
            "let _widgetInitParam = " . json_encode($vueInitParam) . ';',
            $vueScript,
            "_widget.el = '#{$this->id}';",
            "new Vue(_widget);",
            "})();",
        ]));

        if (!empty($vueStyle)) {
            ModStart::style($vueStyle);
        }

        return $vueTemplate;
    }
}
