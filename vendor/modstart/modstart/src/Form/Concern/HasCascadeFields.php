<?php

namespace ModStart\Form\Concern;

use ModStart\Field\CascadeGroup;

trait HasCascadeFields
{
    /**
     * @param \Closure $closure
     * @param array $param
     *
     * @return CascadeGroup
     */
    public function cascadeGroup(\Closure $closure, $param = array())
    {
        $group = new CascadeGroup($param['id'] . '_group_' . $param['index']);
        $group->renderMode($this->fieldDefaultRenderMode());
        $group->context($this);
        $this->pushField($group);
        call_user_func($closure, $this);
        $group->end();
        return $group;
    }
}
