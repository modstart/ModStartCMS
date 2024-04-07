<?php

namespace ModStart\Widget\Traits;

use ModStart\ModStart;

trait HasVueTrait
{
    /**
     * @return string
     */
    abstract public function script();

    /**
     * @return string
     */
    abstract public function template();

    public function content()
    {
        ModStart::js([
            'asset/vendor/vue.js',
            'asset/vendor/element-ui/index.js',
        ]);
        ModStart::script(join('', [
            "Vue.use(ELEMENT, {size: 'mini', zIndex: 3000});",
            $this->script()
        ]));
        return $this->template();
    }
}
