<?php

namespace Module\Member\Util;

use Illuminate\Support\Facades\View;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use Module\Member\Type\MemberMessageStatus;

/**
 * @Util 用户消息
 * Class MemberMessageUtil
 * @package Module\Member\Util
 */
class MemberMessageUtil
{

    public static function getUnreadMessageCount($userId)
    {
        if (empty($userId)) {
            return 0;
        }
        return ModelUtil::count('member_message', ['userId' => $userId, 'status' => MemberMessageStatus::UNREAD]);
    }

    public static function setMemberMessageRead($userId, $ids = [])
    {
        if (empty($ids)) {
            return;
        }
        ModelUtil::model('member_message')->where(['userId' => $userId])->whereIn('id', $ids)->update(['status' => MemberMessageStatus::READ]);
        foreach ($ids as $id) {
            self::updateMessageCount($id);
        }
    }

    public function setMemberMessageReadAll($userId)
    {
        ModelUtil::model('member_message')->where(['userId' => $userId])->update(['status' => MemberMessageStatus::READ]);
        self::updateMessageCount($userId);
    }

    public static function updateMessageCount($userId)
    {
        MemberUtil::update($userId, [
            'messageCount' => self::getUnreadMessageCount($userId),
        ]);
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
        self::updateMessageCount($userId);
    }

    public static function deleteAll($userId)
    {
        ModelUtil::model('member_message')->where(['userId' => $userId])->delete();
        self::updateMessageCount($userId);
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
        self::updateMessageCount($userId);
    }

    public static function updateRead($userId, $ids = [])
    {
        self::update($userId, $ids, ['status' => MemberMessageStatus::READ]);
        self::updateMessageCount($userId);
    }

    public static function updateReadAll($userId)
    {
        ModelUtil::model('member_message')->where(['userId' => $userId])->update(['status' => MemberMessageStatus::READ]);
        self::updateMessageCount($userId);
    }

    /**
     * @Util 发送消息
     * @param $userId integer 用户ID
     * @param $content string 消息HTML内容
     * @param $fromId int 来源用户ID，0表示系统消息
     * @return array
     */
    public static function send($userId, $content, $fromId = 0)
    {
        ModelUtil::insert('member_message', [
            'userId' => $userId,
            'fromId' => $fromId,
            'status' => MemberMessageStatus::UNREAD,
            'content' => $content,
        ]);
        self::updateMessageCount($userId);
        return Response::generate(0, null);
    }

    public static function sendTemplate($memberUserId, $template, $templateData = [], $fromMemberUserId = 0, $module = null)
    {
        $theme = modstart_config()->getWithEnv('siteTemplate', 'default');
        $view = 'theme.' . $theme . '.message.' . $template;
        if (!view()->exists($view)) {
            $view = 'theme.default.message.' . $template;
            if (!view()->exists($view)) {
                if ($module) {
                    $view = 'module::' . $module . '.View.m.message.' . $template;
                    if (!view()->exists($view)) {
                        $view = 'module::' . $module . '.View.message.' . $template;
                        if (!view()->exists($view)) {
                            $view = 'module::' . $module . '.View.pc.message.' . $template;
                        }
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
            BizException::throws(L('View Not Found : %s', $template));
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
