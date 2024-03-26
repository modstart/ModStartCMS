<?php

namespace ModStart\Admin\Widget;

use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\RenderUtil;
use ModStart\ModStart;
use ModStart\Widget\AbstractWidget;

class DashboardItem extends AbstractWidget
{
    private $type = 1;
    private $color;
    private $icon;
    private $title;
    private $value;
    private $param;


    public static function makeTitleDataList($icon, $title, $dataList, $color = null, $param = [])
    {
        if (null === $color) {
            $color = ColorUtil::randomColor();
        }
        $item = new DashboardItem();
        $item->icon = $icon;
        $item->title = $title;
        $item->value = $dataList;
        $item->color = $color;
        $item->param = $param;
        $item->type = 1;
        return $item;
    }

    public function render()
    {
        ModStart::js('asset/common/countUp.js');
        $viewData = [
            'icon' => $this->icon,
            'title' => $this->title,
            'value' => $this->value,
            'color' => $this->color,
            'param' => $this->param,
        ];
        switch ($this->type) {
            case 1:
                return RenderUtil::view('modstart::admin.widget.dashboardItem', $viewData);
        }
        return 'Unknown type';
    }

}
