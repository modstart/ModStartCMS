<?php


namespace Module\Member\Util;


use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Field\AutoRenderedFieldValue;
use Module\Member\Model\MemberUser;

class MemberCmsUtil
{
    /**
     * @param $memberUserId int 用户ID
     * @param $field string 字段名称，默认为username
     * @return AutoRenderedFieldValue
     */
    public static function showFromId($memberUserId, $field = null)
    {
        if (!$memberUserId) {
            return AutoRenderedFieldValue::make('<span class="ub-text-muted">-</span>');
        }
        $memberUser = ModelUtil::getWithCache(MemberUser::class, ['id' => $memberUserId]);
        return self::show($memberUser, $field);
    }

    /**
     * @param $memberUser
     * @param $field string 字段名称，默认为username
     * @return AutoRenderedFieldValue
     */
    public static function show($memberUser, $field = null)
    {
        if (!empty($memberUser)) {
            if (null === $field) {
                $field = [
                    'username',
                ];
            }
            if (!is_array($field)) {
                $field = [$field];
            }
            if ($memberUser['isDeleted']) {
                $text = '<已删除用户>';
            } else {
                $text = '<未知用户>';
                foreach ($field as $f) {
                    if (!empty($memberUser[$f])) {
                        $text = $memberUser[$f];
                        break;
                    }
                }
            }
            return AutoRenderedFieldValue::make('<a href="javascript:;" class="ub-icon-text" data-dialog-request="'
                . action('\\Module\\Member\\Admin\\Controller\\MemberController@show', ['_id' => $memberUser['id']]) . '">
            <img class="icon" src="' . AssetsUtil::fixOrDefault($memberUser['avatar'], 'asset/image/avatar.svg') . '" />
            <span class="text">' . htmlspecialchars($text) . '</span></a>');
        }
        return AutoRenderedFieldValue::make('');
    }
}
