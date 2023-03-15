<?php


namespace Module\Member\Field;


use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Type\FieldRenderMode;
use Module\Member\Util\MemberUtil;

class AutoRenderedMemberUsersField
{
    public static function make($renderMode, $param)
    {
        switch ($renderMode) {
            case FieldRenderMode::GRID:
            case FieldRenderMode::DETAIL:
                $names = array_map(function ($item) {
                    return '<span class="ub-tag sm">' . htmlspecialchars($item) . '</span>';
                }, MemberUtil::listViewName($param['memberUserIds']));
                return AutoRenderedFieldValue::make(join(' ', $names));
            case FieldRenderMode::FORM:
                $memberUsers = MemberUtil::listUsers($param['memberUserIds']);
                $memberUserIds = ArrayUtil::flatItemsByKey($memberUsers, 'id');
                $memberUsers = array_map(function ($item) {
                    return [
                        'value' => intval($item['id']),
                        'name' => MemberUtil::viewName($item),
                        'avatar' => AssetsUtil::fixOrDefault($item['avatar'], 'asset/image/avatar.svg'),
                    ];
                }, $memberUsers);
                return AutoRenderedFieldValue::makeView('module::Member.View.field.memberUsers', [
                    'memberUserIds' => $memberUserIds,
                    'memberUsers' => $memberUsers,
                    'param' => $param,
                ]);
        }
    }
}
