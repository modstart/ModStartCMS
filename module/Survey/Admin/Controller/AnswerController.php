<?php


namespace Module\Survey\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Util\MemberFieldUtil;

class AnswerController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        MemberFieldUtil::register();
        $builder
            ->init('survey_answer')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->select('activityId', '活动')->optionModel('survey_activity', 'id', 'name');
                $builder->adminMemberInfo('memberUserId', '用户');
                $builder->display('_answer', '回答')->hookRendering(function (AbstractField $field, $item, $index) {
                    $answerItems = ModelUtil::all('survey_answer_item', ['answerId' => $item->id], ['*'], ['id', 'asc']);
                    ModelUtil::join($answerItems, 'questionId', '_question', 'survey_question', 'id');
                    return AutoRenderedFieldValue::makeView('module::Survey.View.admin.answer.view', [
                        'item' => $item,
                        'answerItems' => $answerItems,
                    ]);
                });
                $builder->display('created_at', L('Created At'));
            })
            ->canAdd(false)->canEdit(false)// ->canExport(true)->urlExport('asdf')
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->eq('activityId', '活动')->selectModel('survey_activity', 'id', 'name');
            })
            ->hookDeleted(function (Form $form) {
                $form->item()->each(function ($item) {
                    ModelUtil::delete('survey_answer_item', ['answerId' => $item->id]);
                });
            })
            ->title('问卷提交');
    }
}
