<?php

namespace Module\Member\Util;

use Illuminate\Support\Facades\View;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use Module\Member\Type\MemberMessageStatus;

class MemberMessageUtil
{

    public static function getUnreadMessageCount($userId)
    {
        return ModelUtil::count('member_message', ['userId' => $userId, 'status' => MemberMessageStatus::UNREAD]);
    }

    public static function setMemberMessageRead($userId, $ids = [])
    {
        if (empty($ids)) {
            return;
        }
        ModelUtil::model('member_message')->where(['userId' => $userId])->whereIn('id', $ids)->update(['status' => MemberMessageStatus::READ]);
    }

    public function setMemberMessageReadAll($userId)
    {
        ModelUtil::model('member_message')->where(['userId' => $userId])->update(['status' => MemberMessageStatus::READ]);
    }

    public static function paginate($userId, $page, $pageSize, $option = [])
    {
        $option['where']['userId'] = $userId;
        $paginateData = ModelUtil::paginate('member_message', $page, $pageSize, $option);
        $records = [];
        foreach ($paginateData['records'] as $record) {
            $item = [];
            $item['id'] = $record['id'];
            $item['status'] = $record['status'];
            $item['fromId'] = $record['fromId'];
            $item['content'] = $record['content'];
            $item['createTime'] = $record['created_at'];
            $records[] = $item;
        }
        return [
            'records' => $records,
            'total' => $paginateData['total'],
        ];
    }

    public static function delete($userId, $ids = [])
    {
        if (empty($ids)) {
            return;
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        ModelUtil::model('member_message')->whereIn('id', $ids)->where(['userId' => $userId])->delete();
    }

    public static function update($userId, $ids = [], $update = [])
    {
        if (empty($ids) || empty($update)) {
            return;
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        ModelUtil::model('member_message')->whereIn('id', $ids)->where(['userId' => $userId])->update($update);
    }

    public static function updateRead($userId, $ids = [])
    {
        self::update($userId, $ids, ['status' => MemberMessageStatus::READ]);
    }

    public static function updateReadAll($userId)
    {
        ModelUtil::model('member_message')->where(['userId' => $userId])->update(['status' => MemberMessageStatus::READ]);
    }

    public static function send($userId, $content, $fromId = 0)
    {
        ModelUtil::insert('member_message', [
            'userId' => $userId,
            'fromId' => $fromId,
            'status' => MemberMessageStatus::UNREAD,
            'content' => $content,
        ]);
        return Response::generate(0, null);
    }

    public static function sendTemplate($memberUserId, $template, $templateData = [], $fromMemberUserId = 0, $module = null)
    {
        $theme = modstart_config('siteTemplate', 'default');
        $view = 'theme.' . $theme . '.message.' . $template;
        if (!view()->exists($view)) {
            $view = 'theme.default.message.' . $template;
            if (!view()->exists($view)) {
                if ($module) {
                    $view = 'module::' . $module . '.View.' . $theme . '.message.' . $template;
                    if (!view()->exists($view)) {
                        $view = 'module::' . $module . '.View.default.message.' . $template;
                    }
                } else {
                    $view = 'module::Member.View.' . $theme . '.message.' . $template;
                    if (!view()->exists($view)) {
                        $view = 'module::Member.View.default.message.' . $template;
                    }
                }
            }
        }
        if (!view()->exists($view)) {
            throw new \Exception('message view not found : ' . $view);
        }
        $message = View::make($view, $templateData)->render();
        self::send($memberUserId, $message, $fromMemberUserId);
    }

    public static function sendTemplateFromView($view, $viewData, $memberUserId, $fromMemberUserId = 0)
    {
        if (!view()->exists($view)) {
            throw new \Exception('message view not found : ' . $view);
        }
        $message = View::make($view, $viewData)->render();
        self::send($memberUserId, $message, $fromMemberUserId);
    }

}
