<?php


namespace Module\Member\Converter;

use ModStart\Core\Dao\ModelUtil;
use Module\Vendor\Html\HtmlConverterInterceptor;

class AtMemberHtmlInterceptor implements HtmlConverterInterceptor
{
    public function convert($html)
    {
        preg_match_all('/@(.*?):/', $html, $mat);
        if (empty($mat[1])) {
            return $html;
        }

        $userNames = [];
        foreach ($mat[1] as $index => $userName) {
            $userName = trim($userName);
            if (empty($userName)) {
                continue;
            }
            $userNames[$mat[0][$index]] = $userName;
        }

        if (empty($userNames)) {
            return $html;
        }

        $memberUsers = ModelUtil::model('member_user')->whereIn('username', array_values($userNames))->get()->toArray();
        if (empty($memberUsers)) {
            return $html;
        }

        $memberUserMap = [];
        foreach ($memberUsers as $memberUser) {
            $memberUserMap[$memberUser['username']] = $memberUser;
        }

        foreach ($userNames as $atText => $userName) {
            if (empty($memberUserMap[$userName])) {
                continue;
            }
            $memberUserLink = str_replace('{id}', $memberUserMap[$userName]['id'], '/member/{id}');
            $html = str_replace($atText, '<a href="' . $memberUserLink . '" target="_blank">@' . $memberUserMap[$userName]['username'] . '</a>: ', $html);
        }

        return $html;
    }

}