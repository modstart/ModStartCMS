<?php

namespace Module\Member\Middleware;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Type\MemberStatus;
use Module\Member\Util\MemberUtil;

class WebAuthMiddleware
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
            }
        }
        if ($memberUserId && !$memberUser) {
            $memberUserId = 0;
            Session::forget('memberUserId');
        }
        View::share('_memberUserId', $memberUserId);
        if (empty($memberUserId)) {
            if (is_subclass_of($controller, MemberLoginCheck::class)) {
                if (property_exists($controller, 'memberLoginCheckIgnores')
                    && is_array($controller::$memberLoginCheckIgnores) && in_array($action, $controller::$memberLoginCheckIgnores)
                ) {
                    //pass
                } else {
                    return Response::send(-1, null, null, modstart_web_url('login') . '?redirect=' . urlencode(Request::currentPageUrl()));
                }
            }
        }
        Session::put('memberUserId', $memberUserId);
        Session::flash('_memberUser', $memberUser);
        View::share('_memberUser', $memberUser);
        return $next($request);
    }
}
