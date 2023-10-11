<?php


namespace Module\Vendor\Middleware;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\TimeUtil;
use Module\Vendor\Util\NoneLoginOperateUtil;

class NoneLoginOperateAuthMiddleware
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
        $input = InputPackage::buildFromInput();
        $timestamp = $input->getInteger('timestamp');
        BizException::throwsIf('已超时效（操作时间显示为24小时内，timestamp=' . time() . '）', !($timestamp <= time() && $timestamp > time() - TimeUtil::PERIOD_DAY));
        $nonce = $input->getTrimString('nonce');
        $sign = $input->getTrimString('sign');
        BizException::throwsIfEmpty('nonce为空', $nonce);
        BizException::throwsIfEmpty('sign为空', $sign);
        $signCalc = NoneLoginOperateUtil::sign(Request::path(), $nonce, $timestamp, $input->getTrimString('param'));
        BizException::throwsIf('sign错误', $sign != $signCalc);
        return $next($request);
    }
}
