<?php


namespace Module\Member\Util;


use ModStart\Core\Exception\BizException;

class MemberParamUtil
{
    public static function param()
    {
        return [
            '{id}' => '用户ID',
            '{username}' => '用户名',
        ];
    }

    public static function replaceParam($content, $memberUser)
    {
        BizException::throwsIfEmpty('用户为空', $memberUser);
        if (!is_array($memberUser)) {
            $memberUser = MemberUtil::getCached($memberUser);
        }
        $keys = ['id', 'username'];
        $param = [];
        foreach ($keys as $k) {
            $param['{' . $k . '}'] = isset($memberUser[$k]) ? $memberUser[$k] : '';
        }
        return str_replace(array_keys($param), array_values($param), $content);
    }

    public static function paramTitle()
    {
        return '<a href="javascript:;" data-dialog-request="' . modstart_admin_url('member/config/param') . '"><i class="iconfont icon-code"></i> 动态变量</a>';
    }
}
