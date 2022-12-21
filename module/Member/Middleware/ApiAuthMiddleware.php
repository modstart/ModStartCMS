<?php

namespace Module\Member\Middleware;

use Illuminate\Support\Facades\Session;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Type\MemberStatus;
use Module\Member\Util\MemberUtil;
use Module\Vendor\Support\ResponseCodes;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        list($controller, $action) = Request::getControllerAction();
        $memberUserId = intval(Session::get('memberUserId', 0));
        // Log::info("ApiAuthMiddleware - MemberUserId - $memberUserId");
        $memberUser = null;
        if ($memberUserId) {
            $memberUser = MemberUtil::get($memberUserId);
            MemberUtil::processDefault($memberUser);
            if ($memberUser) {
                switch ($memberUser['status']) {
                    case MemberStatus::FORBIDDEN:
                        $memberUser = null;
                        break;
                }
                // 已删除用户
                if ($memberUser['isDeleted']) {
                    $memberUser = null;
                }
            }
        }
        if ($memberUserId && !$memberUser) {
            $memberUserId = 0;
            Session::forget('memberUserId');
        }
        if (empty($memberUserId)) {
            if (is_subclass_of($controller, MemberLoginCheck::class)) {
                if (property_exists($controller, 'memberLoginCheckIgnores')
                    && is_array($controller::$memberLoginCheckIgnores) && in_array($action, $controller::$memberLoginCheckIgnores)
                ) {
                    //pass
                } else {
                    return Response::json(ResponseCodes::LOGIN_REQUIRED, '请登录', null, modstart_web_url('login', [
                        'redirect' => Request::currentPageUrl(),
                    ]));
                }
            }
        }
        Session::put('memberUserId', $memberUserId);
        Session::flash('_memberUser', $memberUser);
        return $next($request);
    }
}
