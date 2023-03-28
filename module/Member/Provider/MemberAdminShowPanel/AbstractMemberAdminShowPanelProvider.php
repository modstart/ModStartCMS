<?php


namespace Module\Member\Provider\MemberAdminShowPanel;


use Illuminate\Support\Facades\View;

abstract class AbstractMemberAdminShowPanelProvider
{
    abstract public function name();

    abstract public function title();

    public function renderView()
    {
        return null;
    }

    public function render($memberUser, $param = [])
    {
        $view = $this->renderView();
        if (empty($view)) {
            return null;
        }
        return View::make($view, [
            'memberUser' => $memberUser,
            'param' => $param,
        ])->render();
    }
}
