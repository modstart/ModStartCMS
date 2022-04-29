<?php

namespace Module\TagManager\Web\Controller;


use ModStart\Core\Util\AgentUtil;
use ModStart\Module\ModuleBaseController;
use Module\TagManager\Biz\TagManagerBiz;
use Module\TagManager\Model\TagManager;

class IndexController extends ModuleBaseController
{
    public function index()
    {
        $bizList = [];
        $canvasWidth = 2000;
        $canvasHeight = 1000;
        if (AgentUtil::isMobile()) {
            $canvasHeight = 2000;
        }
        foreach (TagManagerBiz::all() as $biz) {
            $records = TagManager::allVisible($biz->name());
            $total = max(array_sum(array_map(function ($o) {
                return $o['cnt'];
            }, $records)), 1);
            $counts = array_map(function ($o) use ($total, $biz) {
                $size = 20 + intval(($o['cnt'] / $total) * 800);
                $size = min($size, 500);
                return [$o['tag'], $size, $biz->searchUrl($o['tag'])];
            }, $records);
            $records = array_map(function ($o) use ($biz) {
                $o['_url'] = $biz->searchUrl($o['tag']);
                return $o;
            }, $records);

            $bizList[] = [
                'name' => $biz->name(),
                'title' => $biz->title(),
                'records' => $records,
                'counts' => $counts,
            ];
        }
        return $this->view('tagManager.index', [
            'bizList' => $bizList,
            'canvasWidth' => $canvasWidth,
            'canvasHeight' => $canvasHeight,
        ]);
    }
}
