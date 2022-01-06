<?php


namespace Module\Nav\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Type\TypeUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\ModStart;
use ModStart\Repository\Filter\ScopeFilter;
use ModStart\Support\Concern\HasFields;
use Module\Nav\Type\NavOpenType;
use Module\Nav\Type\NavPosition;
use Module\Nav\Util\NavUtil;

class NavController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        if ($builder->mode() == AdminCRUDBuilder::MODE_FORM) {
            $navPositions = [];
            foreach (ModelUtil::all('nav', ['pid' => 0], ['*']) as $item) {
                $navPositions[$item['id']] = TypeUtil::name(NavPosition::class, $item['position']) . ' > ' . $item['name'];
            }
            ModStart::script('window.__navPositionMap=' . json_encode($navPositions) . ';');
            ModStart::scriptFile('module/Nav/Admin/Controller/NavEdit.js');
        }
        $builder
            ->init('nav')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $builder->select('position', '位置')
                    ->optionType(NavPosition::class)
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        switch ($field->renderMode()) {
                            case FieldRenderMode::DETAIL:
                            case FieldRenderMode::GRID:
                                if ($item->pid) {
                                    return AutoRenderedFieldValue::make('');
                                }
                                return AutoRenderedFieldValue::make(
                                    TypeUtil::name(NavPosition::class, $item->position)
                                );
                        }
                    });
                $builder->text('name', '名称');
                $builder->link('link', '链接');
                $builder->radio('openType', '打开方式')->optionType(NavOpenType::class)->defaultValue(NavOpenType::CURRENT_WINDOW);
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            });
        foreach (NavPosition::getList() as $key => $value) {
            $builder->scopeFilter($key, $value, function (ScopeFilter $filter) use ($key) {
                return $filter->where('position', $key);
            });
        }
        $builder
            ->hookSaved(function (Form $form) {
                /** @var \stdClass $item */
                $item = $form->item();
                if ($item->pid > 0) {
                    $parent = ModelUtil::get('nav', $item->pid);
                    ModelUtil::update('nav', $item->id, [
                        'position' => $parent['position'],
                    ]);
                }
            })
            ->hookChanged(function (Form $form) {
                NavUtil::clearCache();
            })
            ->canBatchDelete(true)
            ->asTree('id', 'pid', 'sort', 'name')
            ->treeMaxLevel(2)
            ->title('导航')
            ->dialogSizeSmall();
    }
}
