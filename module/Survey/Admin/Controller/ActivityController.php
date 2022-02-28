<?php


namespace Module\Survey\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Type\TypeUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\TextLink;
use Module\Survey\Type\JoinType;
use Module\Survey\Type\SurveyQuestionType;

class ActivityController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('survey_activity')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->text('name', '名称')->hookRendering(function (AbstractField $field, $item, $index) {
                    return AutoRenderedFieldValue::make(
                        TextLink::primary(htmlspecialchars($item->name), modstart_web_url('survey/activity/' . $item->alias), 'target="_blank"')
                    );
                });
                $builder->display('_url', '链接')->hookRendering(function (AbstractField $field, $item, $index) {
                    $url = Request::domainUrl() . modstart_web_url('survey/activity/' . $item->alias);
                    return AutoRenderedFieldValue::make(
                        TextLink::primary(htmlspecialchars($url), $url, 'target="_blank"')
                    );
                })->editable(false);
                $builder->image('cover', '封面');
                $builder->switch('enable', '开启');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('name', '名称');
            })
            ->title('问卷调查');
    }

    public function add()
    {
        return $this->doAddEdit();
    }

    public function edit()
    {
        return $this->doAddEdit();
    }

    private function doAddEdit()
    {
        $id = CRUDUtil::id();
        if ($id) {
            $activity = ModelUtil::get('survey_activity', ['id' => $id]);
            if (empty($activity)) {
                return Response::send(-1, '记录不存在');
            }
            $questions = ModelUtil::all('survey_question', ['activityId' => $activity['id']], ['*'], ['sort', 'asc']);
            ModelUtil::decodeRecordsJson($questions, 'choice');
            $questions = array_build($questions, function ($key, $value) {
                $value = array_only($value, ['id', 'type', 'required', 'body', 'choice']);
                $value['required'] = $value['required'] ? true : false;
                return [$key, $value];
            });
        } else {
            $activity = null;
            $questions = [];
        }

        if (Request::isPost()) {

            $input = InputPackage::buildFromInputJson('data');
            $data = $input->all();
            if (empty($data) || !is_array($data)) {
                return Response::send(-1, '数据为空');
            }
            $saveActivity = [];
            $saveActivity['name'] = trim(empty($data['name']) ? '' : $data['name']);
            $saveActivity['enable'] = (empty($data['enable']) ? false : true);
            $saveActivity['loginRequired'] = (empty($data['loginRequired']) ? false : true);
            $saveActivity['joinType'] = intval(empty($data['joinType']) ? '' : $data['joinType']);
            $saveActivity['startTime'] = trim(empty($data['startTime']) ? '' : $data['startTime']);
            $saveActivity['endTime'] = trim(empty($data['endTime']) ? '' : $data['endTime']);
            $saveActivity['cover'] = trim(empty($data['cover']) ? '' : $data['cover']);
            $saveActivity['description'] = $input->getRichContent('description');

            $saveQuestions = [];
            foreach ((empty($data['questions']) ? [] : $data['questions']) as $index => $question) {
                $saveQuestion = [];
                $saveQuestion['sort'] = $index;
                $saveQuestion['id'] = intval(empty($question['id']) ? 0 : $question['id']);
                $saveQuestion['required'] = (empty($question['required']) ? false : true);
                $saveQuestion['type'] = intval(empty($question['type']) ? 0 : $question['type']);
                $saveQuestion['body'] = trim(empty($question['body']) ? '' : $question['body']);
                $saveQuestion['choice'] = empty($question['choice']) ? [] : $question['choice'];
                $saveQuestions[] = $saveQuestion;
            }

            if (empty($saveActivity['name'])) {
                return Response::send(-1, '问卷名称不能为空');
            }
            if (!TypeUtil::name(JoinType::class, $saveActivity['joinType'])) {
                return Response::send(-1, '问卷参与限制为空');
            }
            if (empty($saveQuestions)) {
                return Response::send(-1, '问卷题目为空');
            }
            foreach ($saveQuestions as $index => $saveQuestion) {
                if (empty($saveQuestion['body'])) {
                    return Response::send(-1, '问卷题目' . ($index + 1) . '题目描述为空');
                }
                switch ($saveQuestion['type']) {
                    case SurveyQuestionType::SINGLE_CHOICE:
                    case SurveyQuestionType::MULTI_CHOICE:
                        if (empty($saveQuestion['choice'])) {
                            return Response::send(-1, '问卷题目' . ($index + 1) . '题目选项为空');
                        }
                        break;
                }
            }

            if ($activity) {
                // 修改
                try {
                    $saveActivity = ModelUtil::update('survey_activity', ['id' => $activity['id']], $saveActivity);
                    $oldQuestionIds = array_pluck($questions, 'id');
                    $newQuestionIds = array_pluck($saveQuestions, 'id');
                    $questionIdsToDeleted = array_diff($oldQuestionIds, $newQuestionIds);
                    ModelUtil::model('survey_question')->whereIn('id', $questionIdsToDeleted)->delete();
                    foreach ($saveQuestions as $saveQuestion) {
                        $saveQuestion['choice'] = json_encode($saveQuestion['choice']);
                        if ($saveQuestion['id']) {
                            ModelUtil::update('survey_question', ['id' => $saveQuestion['id']], $saveQuestion);
                        } else {
                            $saveQuestion['activityId'] = $activity['id'];
                            ModelUtil::insert('survey_question', $saveQuestion);
                        }
                    }
                    return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
                } catch (\Exception $e) {
                    return Response::send(-1, '保存失败:' . $e->getMessage());
                }
            } else {
                // 增加
                try {
                    $saveActivity['template'] = 'default';
                    $saveActivity['alias'] = RandomUtil::lowerString(8);
                    $saveActivity = ModelUtil::insert('survey_activity', $saveActivity);
                    foreach ($saveQuestions as $saveQuestion) {
                        $saveQuestion['activityId'] = $saveActivity['id'];
                        $saveQuestion['choice'] = json_encode($saveQuestion['choice']);
                        ModelUtil::insert('survey_question', $saveQuestion);
                    }
                    return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
                } catch (\Exception $e) {
                    return Response::send(-1, '保存失败:' . $e->getMessage());
                }
            }
        }
        return view('module::Survey.View.admin.activity.edit', [
            'activity' => $activity,
            'questions' => $questions,
        ]);
    }
}
