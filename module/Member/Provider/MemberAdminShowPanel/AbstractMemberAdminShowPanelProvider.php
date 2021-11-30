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
        return View::make($this->renderView(), [
            'memberUser' => $memberUser,
            'param' => $param,
        ])->render();
    }
}
