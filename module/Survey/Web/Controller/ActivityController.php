<?php

namespace Module\Survey\Web\Controller;


use Illuminate\Support\Facades\Input;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\TimeUtil;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Survey\Type\JoinType;
use Module\Survey\Type\SurveyQuestionType;

class ActivityController extends ModuleBaseController
{
    private $activity;

    public function index($alias)
    {
        $this->activity = ModelUtil::get('survey_activity', ['alias' => $alias]);
        BizException::throwsIfEmpty('问卷调查不存在', $this->activity);
        BizException::throwsIfEmpty('问卷调查未开启', $this->activity['enable']);
        if (!TimeUtil::isDatetimeEmpty($this->activity['startTime'])) {
            if (time() < strtotime($this->activity['startTime'])) {
                return Response::send(-1, '问卷调查未开始');
            }
        }
        if (!TimeUtil::isDatetimeEmpty($this->activity['endTime'])) {
            if (time() > strtotime($this->activity['endTime'])) {
                return Response::send(-1, '问卷调查已经结束');
            }
        }
        switch ($this->activity['joinType']) {
            case JoinType::ONCE_PER_USER:
                if (MemberUser::isNotLogin()) {
                    return Response::redirect(modstart_web_url('login', ['redirect' => Request::currentPageUrl()]));
                }
                if (ModelUtil::exists('survey_answer', ['activityId' => $this->activity['id'], 'memberUserId' => MemberUser::id()])) {
                    return Response::send(-1, '你已经填写过问卷调查了');
                }
                break;
            case JoinType::NO_LIMIT:
                break;
        }
        if ($this->activity['loginRequired']) {
            if (!MemberUser::isNotLogin()) {
                return Response::redirect(modstart_web_url('login', ['redirect' => Request::currentPageUrl()]));
            }
        }
        $questions = ModelUtil::all('survey_question', ['activityId' => $this->activity['id']], ['*'], ['sort', 'asc']);
        ModelUtil::decodeRecordsJson($questions, 'choice');
        if (Request::isPost()) {
            $answerItems = [];
            foreach ($questions as $question) {
                $answerItemBody = Input::get('field-' . $question['id']);
                if ($question['required']) {
                    if (empty($answerItemBody)) {
                        return Response::send(-1, '题目 ' . $question['body'] . ' 必须填写哦~');
                    }
                }
                switch ($question['type']) {
                    case SurveyQuestionType::SINGLE_CHOICE:
                        break;
                    case SurveyQuestionType::MULTI_CHOICE:
                        if ($answerItemBody && !is_array($answerItemBody)) {
                            return Response::send(-1, '数据错误');
                        }
                        $answerItemBody = json_encode($answerItemBody);
                        break;
                    case SurveyQuestionType::TEXT:
                        break;
                    case SurveyQuestionType::BIG_TEXT:
                        break;
                }
                $answerItems[] = [
                    'questionId' => $question['id'],
                    'questionType' => $question['type'],
                    'body' => $answerItemBody,
                ];
            }

            try {
                $answer = ModelUtil::insert('survey_answer', [
                    'activityId' => $this->activity['id'],
                    'memberUserId' => MemberUser::id(),
                ]);
                foreach ($answerItems as $answerItem) {
                    ModelUtil::insert('survey_answer_item', array_merge([
                        'memberUserId' => MemberUser::id(),
                        'activityId' => $this->activity['id'],
                        'answerId' => $answer['id'],
                    ], $answerItem));
                }
                return Response::send(0, '提交成功', null, '[reload]');
            } catch (\Exception $e) {
                return Response::send(-1, '提交失败:' . $e->getMessage());
            }
        }
        return $this->view('survey.activity.index', [
            'activity' => $this->activity,
            'questions' => $questions,
        ]);
    }
}
