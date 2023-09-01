<?php


namespace Module\Cms\View;


use Illuminate\Support\Facades\View;

class CmsView
{
    public static function likeBtn($id, $param = [])
    {
        return View::make('module::Cms.View.inc.likeBtn', [
            'id' => $id,
            'param' => $param,
        ])->render();
    }
}
