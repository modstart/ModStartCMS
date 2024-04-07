<?php

namespace ModStart\Widget\Traits;

use ModStart\Core\Util\ReUtil;
use ModStart\ModStart;

trait HasVueFileTrait
{
    public function content()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filePath = $reflector->getFileName();
        $filePath = preg_replace('/\.php$/', '.vue', $filePath);

        $vueTemplate = '<div class="ub-alert danger">Vue file not found: ' . $filePath . '</div>';
        $vueScript = 'export default {}';

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
        }

        $vueScript = preg_replace('/export default/', 'let _widget = ', $vueScript) . ';';

        if (method_exists($this, 'contentRenderBefore')) {
            call_user_func([$this, 'contentRenderBefore']);
        }

        ModStart::js([
            'asset/vendor/vue.js',
            'asset/vendor/element-ui/index.js',
        ]);

        ModStart::script(join('', [
            "Vue.use(ELEMENT, {size: 'mini', zIndex: 3000});",
            $vueScript,
            "_widget.el = '#{$this->id}';",
            "new Vue(_widget);",
        ]));

        return $vueTemplate;
    }
}
