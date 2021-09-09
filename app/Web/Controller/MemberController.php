<?php


namespace App\Web\Controller;


use ModStart\Core\Input\Response;
use Module\Member\Support\MemberLoginCheck;

class MemberController extends BaseController implements MemberLoginCheck
{
    public static $memberLoginCheckIgnores = ['show'];

    public function index()
    {
        return Response::redirect(modstart_web_url('member_profile'));
    }

    public function show($id)
    {
        return Response::redirect(modstart_web_url('note_member/' . $id));
    }


}
