<?php


namespace ModStart\Grid;

use Closure;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\IdUtil;
use ModStart\Core\Util\RenderUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Detail\Detail;
use ModStart\Field\AbstractField;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\Concerns\HasGridFilter;
use ModStart\Grid\Concerns\HasItemOperate;
use ModStart\Grid\Concerns\HasPaginator;
use ModStart\Grid\Concerns\HasSort;
use ModStart\Grid\Type\GridEngine;
use ModStart\Repository\Filter\HasRepositoryFilter;
use ModStart\Repository\Filter\HasScopeFilter;
use ModStart\Repository\Repository;
use ModStart\Support\Concern\HasBuilder;
use ModStart\Support\Concern\HasFields;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Manager\FieldManager;

/**
 * Class Grid
 *
 * @method Grid|mixed engine($value = null)
 * @method Grid|mixed title($value = null)
 * @method Grid|mixed titleAdd($value = null)
 * @method Grid|mixed pageTitleAdd($value = null)
 * @method Grid|mixed titleEdit($value = null)
 * @method Grid|mixed pageTitleEdit($value = null)
 * @method Grid|mixed titleShow($value = null)
 * @method Grid|mixed pageTitleShow($value = null)
 * @method Grid|mixed titleExport($value = null)
 * @method Grid|mixed titleImport($value = null)
 * @method Grid|mixed canAdd($value = null)
 * @method Grid|mixed canEdit($value = null)
 * @method Grid|mixed canDelete($value = null)
 * @method Grid|mixed canShow($value = null)
 * @method Grid|mixed canExport($value = null)
 * @method Grid|mixed canImport($value = null)
 * @method Grid|mixed canCopy($value = null)
 * @method Grid|mixed canMultiSelectItem($value = null)
 * @method Grid|mixed canSingleSelectItem($value = null)
 * @method Grid|mixed urlGrid($value = null)
 * @method Grid|mixed urlAdd($value = null)
 * @method Grid|mixed urlEdit($value = null)
 * @method Grid|mixed textEdit($value = null)
 * @method Grid|mixed urlDelete($value = null)
 * @method Grid|mixed urlShow($value = null)
 * @method Grid|mixed urlExport($value = null)
 * @method Grid|mixed urlImport($value = null)
 * @method Grid|mixed urlSort($value = null)
 * @method Grid|mixed addDialogSize($value = null)
 * @method Grid|mixed editDialogSize($value = null)
 * @method Grid|mixed showDialogSize($value = null)
 * @method Grid|mixed importDialogSize($value = null)
 * @method Grid|mixed addBlankPage($value = null)
 * @method Grid|mixed editBlankPage($value = null)
 * @method Grid|mixed defaultOrder($value = null)
 * @method Grid|mixed treeMaxLevel($value = null)
 * @method Form|mixed treeRootPid($value = null)
 * @method Grid|mixed batchOperatePrepend($value = null)
 * @method Grid|mixed footOperate($value = null)
 * @method Grid|mixed gridOperateAppend($value = null)
 * @method Grid|mixed view($value = null)
 * @method Grid|mixed viewData($value = null)
 * // 配合 useSimple 使用，设置每行的列数，value 是一个数组，分别表示 md 和 sm 的占比（总和为 12）
 * @method Grid|mixed gridRowCols($value = null)
 * @method Grid|mixed defaultPageSize($value = null)
 * @method Grid|mixed pageSizes($value = null)
 * @method Grid|mixed gridToolbar($value = []),
 * @method Grid|mixed pageJumpEnable($enable = null),
 *
 * $value = function(Grid $grid){  $grid->model()->xxxx();  }
 * @method Grid|mixed hookPreQuery($value = null)
 * $value = function(Grid $grid, $items){ return $items; }
 * @method Grid|mixed hookPrepareItems($value = null)
 * @value = function(Grid $grid, $items){ return 'html string'; }
 * @method Grid|mixed hookSimpleRecordsRendering($value = null)
 *
 */
class Grid
{
    use HasBuilder,
        HasFields,
        HasFluentAttribute,
        HasGridFilter,
        HasItemOperate,
        HasPaginator,
        HasSort,
        HasScopeFilter,
        HasRepositoryFilter;

    /**
     * @var string
     */
    private $id;
    /**
     * @var Model
     */
    private $model;

    protected $fluentAttributes = [
        'view',
        'viewData',
        'engine',
        'title',
        'titleAdd',
        'pageTitleAdd',
        'titleEdit',
        'pageTitleEdit',
        'titleShow',
        'pageTitleShow',
        'titleImport',
        'canAdd',
        'canEdit',
        'canDelete',
        'canShow',
        'canExport',
        'canImport',
        'canCopy',
        'canMultiSelectItem',
        'canSingleSelectItem',
        'canBatchDelete',
        'canBatchSelect',
        'batchSelectInOrder',
        'canSort',
        'urlGrid',
        'urlAdd',
        'urlEdit',
        'textEdit',
        'urlDelete',
        'urlShow',
        'urlExport',
        'urlImport',
        'urlSort',
        'addDialogSize',
        'editDialogSize',
        'showDialogSize',
        'importDialogSize',
        'addBlankPage',
        'editBlankPage',
        'enablePagination',
        'defaultOrder',
        'treeMaxLevel',
        'treeRootPid',
        'batchOperatePrepend',
        'footOperate',
        'gridOperateAppend',
        'hookPreQuery',
        'hookPrepareItems',
        'hookSimpleRecordsRendering',
        'gridRowCols',
        'defaultPageSize',
        'pageSizes',
        'gridToolbar',
        'pageJumpEnable',
    ];
    /**
     * 运行引擎 @see GridEngine
     * @var string
     */
    private $engine = 'basic';
    private $title;
    private $titleAdd;
    private $pageTitleAdd;
    private $titleEdit;
    private $pageTitleEdit;
    private $titleShow;
    private $pageTitleShow;
    private $titleImport;
    private $canAdd = true;
    private $canEdit = true;
    private $canDelete = true;
    private $canShow = true;
    private $canExport = false;
    private $canImport = false;
    private $canCopy = false;
    private $canMultiSelectItem = false;
    private $canSingleSelectItem = false;
    private $canBatchDelete = false;
    private $canBatchSelect = false;
    private $batchSelectInOrder = false;
    private $canSort = false;
    private $urlGrid;
    private $urlAdd;
    private $urlEdit;
    private $textEdit;
    private $urlDelete;
    private $urlShow;
    private $urlExport;
    private $urlImport;
    private $urlSort;
    private $addDialogSize = ['95%', '95%'];
    private $editDialogSize = ['95%', '95%'];
    private $showDialogSize = ['95%', '95%'];
    private $importDialogSize = ['95%', '95%'];
    private $addBlankPage = false;
    private $editBlankPage = false;
    private $enablePagination = true;
    private $defaultOrder = [];
    private $treeMaxLevel = 0;
    private $treeRootPid = 0;
    /** @var string 顶部批量操作字符串 */
    private $batchOperatePrepend = '';
    /**
     * @var string 底部批量操作字符串
     */
    private $footOperate = '';
    /** @var string 右上角追加字符串 */
    private $gridOperateAppend = '';
    /** @var array simple模式下栅格所占用的栅格大小null:表示不启用，[6,12]:表示md,sm栅格占比 */
    private $gridRowCols = null;
    /** @var int grid default page size */
    private $defaultPageSize = 10;
    /** @var int[] grid page sizes for selection list */
    private $pageSizes = [10, 50, 100];
    /** @var string[] grid toolbar */
    private $gridToolbar = [];
    /** @var bool page jump enable */
    private $pageJumpEnable = false;
    /** @var Closure 数据查询前置处理 */
    private $hookPreQuery = null;
    /** @var Closure 渲染前置处理Items */
    private $hookPrepareItems = null;
    /** @var Closure 简单模式下自定义渲染列表HTML */
    private $hookSimpleRecordsRendering = null;
    /** @var array 渲染在Table顶部的区域 */
    private $gridTableTops = [];
    /** @var Closure 请求处理额外脚本 */
    private $gridRequestScript = null;
    /** @var Closure 请求处理额外脚本 */
    private $gridBeforeRequestScript = null;
    /** @var bool */
    private $isBuilt = false;

    /**
     * @var bool 是否是使用数据表名称快速生成的动态模型
     */
    private $isDynamicModel = false;
    /**
     * @var string 动态模型数据表名称
     */
    private $dynamicModelTableName;

    /**
     * @var string Grid页面视图
     */
    private $view = 'modstart::core.grid.index';
    /**
     * @var array Grid页面视图数据
     */
    private $viewData = [];
    /**
     * @var string 追加视图内容
     */
    private $bodyAppend = '';

    /**
     * Grid constructor.
     * @param null $repository
     * @param Closure|null $builder
     */
    public function __construct($repository = null, \Closure $builder = null)
    {
        $this->id = IdUtil::generate('Grid');
        $this->model = new Model($this, $repository);
        $this->setupFields();
        $this->fieldDefaultRenderMode(FieldRenderMode::GRID);
        $this->setupRepositoryFilter();
        $this->setupGridFilter();
        $this->setupItemOperate();
        $this->builder($builder);
    }

    /**
     * @param $model
     * @param Closure|null $builder = function(Grid $grid){  }
     * @return Grid
     */
    public static function make($model, \Closure $builder = null)
    {
        if ($model && is_object($model)) {
            return new static($model, $builder);
        }
        if (class_exists($model)) {
            if (
                is_subclass_of($model, \Illuminate\Database\Eloquent\Model::class)
                ||
                is_subclass_of($model, Repository::class)
            ) {
                return new static($model, $builder);
            }
        }
        $grid = new static(DynamicModel::make($model), $builder);
        $grid->isDynamicModel = true;
        $grid->dynamicModelTableName = $model;
        return $grid;
    }

    /**
     * @param $htmlHookRending
     * @param $hookSimpleRecordsRendering \Closure|null 整个列表渲染回调函数
     * @return $this
     *
     * $htmlHookRending = function(AbstractField $field, $item, $index){ return $item->title; }
     * $recordsHtmlHookRending = function($items){ return '<div>records html</div>'; }
     */
    public function useSimple($htmlHookRending, $hookSimpleRecordsRendering = null)
    {
        $this->view = 'modstart::core.grid.simple';
        $this->disableItemOperate();
        if ($htmlHookRending) {
            $this->display('html', 'html')->hookRendering($htmlHookRending);
        }
        $this->hookSimpleRecordsRendering = $hookSimpleRecordsRendering;
        return $this;
    }

    public function asTree($keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository()->setKeyName($keyName);
        $this->repository()->setTreePidColumn($pidColumn);
        $this->repository()->setSortColumn($sortColumn);
        $this->repository()->setTreeTitleColumn($titleColumn);
        $this->engine = GridEngine::TREE;
        $this->enablePagination(false);
        $this->canSort(true);
        return $this;
    }

    public function asTreeMass($rootPid = 0, $keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository()->setKeyName($keyName);
        $this->repository()->setTreePidColumn($pidColumn);
        $this->repository()->setSortColumn($sortColumn);
        $this->repository()->setTreeTitleColumn($titleColumn);
        $this->engine = GridEngine::TREE_MASS;
        $this->enablePagination(false);
        $this->canSort(true);
        return $this;
    }

    /**
     * @param null $value
     * @return Grid|bool
     */
    public function canBatchDelete($value = null)
    {
        if (null === $value) {
            return $this->canBatchDelete;
        }
        $this->canBatchDelete = true;
        $this->canMultiSelectItem(true);
        return $this;
    }

    public function canBatchSelect($value = null)
    {
        if (null === $value) {
            return $this->canBatchSelect;
        }
        $this->canBatchSelect = $value;
        $this->canMultiSelectItem(true);
        return $this;
    }

    /**
     * 多选时，是否按照选择顺序进行批量操作
     * @param $value bool
     * @return $this|bool
     */
    public function batchSelectInOrder($value = null)
    {
        if (null === $value) {
            return $this->batchSelectInOrder;
        }
        $this->batchSelectInOrder = $value;
        return $this;
    }

    public function disableCUD()
    {
        $this->canAdd(false)->canEdit(false)->canDelete(false);
        return $this;
    }

    public function dialogSizeSmall()
    {
        return $this
            ->addDialogSize(['600px', '90%'])
            ->editDialogSize(['600px', '90%'])
            ->showDialogSize(['600px', '90%']);
    }

    public function model()
    {
        return $this->model;
    }

    public function repository()
    {
        return $this->model->repository();
    }

    public function getRepositoryKeyName()
    {
        return $this->model->repository()->getKeyName();
    }

    /**
     * Set the grid filter.
     *
     * @param Closure $callback function(GridFilter $filter){ $filter->eq('id','ID'); }
     */
    public function gridFilter(Closure $callback)
    {
        call_user_func($callback, $this->gridFilter);
        return $this;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function gridRequestScript($closure)
    {
        $this->gridRequestScript = $closure;
        return $this;
    }

    /**
     * @param $view string
     * @return $this
     */
    public function gridBeforeRequestScriptView($view)
    {
        return $this->gridBeforeRequestScript(
            RenderUtil::viewScript($view)
        );
    }

    /**
     * @param $script string
     * @return $this
     */
    public function gridBeforeRequestScript($script)
    {
        $this->gridBeforeRequestScript = $script;
        return $this;
    }

    /**
     * 渲染在顶部
     * @param $view
     * @return $this
     */
    public function gridTableTopView($view, $viewData = [])
    {
        return $this->gridTableTop(View::make($view, $viewData)->render());
    }

    /**
     * 渲染在顶部
     * @param $content
     * @return $this
     */
    public function gridTableTop($content)
    {
        if ($content instanceof Closure) {
            $content = call_user_func($content, $this);
        }
        $this->gridTableTops[] = $content;
        return $this;
    }

    public function bodyAppend($content)
    {
        $this->bodyAppend = $content;
        return $this;
    }

    /**
     * 开始构建Grid，主要处理回调等操作
     */
    public function build()
    {
        if (!$this->isBuilt) {
            $this->runBuilder();
            $this->prepareItemOperateField();
            $this->isBuilt = true;
        }
    }

    public function executeQuery()
    {
        $this->build();
        $input = InputPackage::buildFromInput();
        $this->repository()->setArgument([
            'page' => $input->getPage(),
            'pageSize' => $input->getPageSize(null, null, 1000, $this->defaultPageSize),
            'order' => $input->getArray($this->model->getOrderName()),
            'orderDefault' => $this->defaultOrder,
        ]);
        $this->gridFilter->setSearch($input->getArray('search'));
        return $this->gridFilter->executeQuery();
    }

    public function request()
    {
        $addition = null;
        $this->build();
        $input = InputPackage::buildFromInput();
        $this->repository()->setArgument([
            'page' => $input->getPage(),
            'pageSize' => $input->getPageSize(null, null, 1000, $this->defaultPageSize),
            'order' => $input->getArray($this->model->getOrderName()),
            'orderDefault' => $this->defaultOrder,
        ]);
        $raw = [];
        $treeAncestors = [];
        if ($this->engine === GridEngine::TREE_MASS) {
            $pid = $input->get('_pid', $this->treeRootPid);
            $this->repository()->setArgument([
                'treeRootPid' => $this->treeRootPid,
                'treePid' => $pid,
            ]);
            if ($pid != $this->treeRootPid) {
                $treeAncestors = $this->repository()->getTreeAncestorItems();
            }
            $addition = view('modstart::core.grid.treeAncestor', [
                'treeAncestors' => $treeAncestors,
                'grid' => $this,
            ])->render();
        }
        // print_r($input->getArray('search'));exit();
        $this->gridFilter->setSearch($input->getArray('search'));
        $items = $this->gridFilter->execute();
        // print_r($items->toArray());exit();
        if ($this->engine == GridEngine::TREE) {
            $treeIdName = $this->repository()->getKeyName();
            $treePidName = $this->repository()->getTreePidColumn();
            $treeSortName = $this->repository()->getTreeSortColumn();
            // return [$items, $treeIdName, $treePidName, $treeSortName];
            $items = TreeUtil::itemsMergeLevel($items, $treeIdName, $treePidName, $treeSortName);
            // return [$items, $treeIdName, $treePidName, $treeSortName];
            $raw['records'] = [];
        }
        $paginator = $this->model->paginator();
        if ($this->hookPrepareItems) {
            $items = call_user_func($this->hookPrepareItems, $this, $items);
        }
        // var_dump($items);exit();
        $records = [];
        foreach ($items as $index => $item) {
            // print_r($item);exit();
            /** @var \Illuminate\Database\Eloquent\Model|\stdClass $item */
            $itemColumns = [];
            if ($item instanceof \Illuminate\Database\Eloquent\Model) {
                $itemColumns = array_keys($item->getAttributes());
            } else if ($item instanceof \stdClass) {
                $itemColumns = array_keys(get_object_vars($item));
            } else {
                BizException::throws('Grid item support Model|stdClass only');
            }
            $record = [];
            $recordRaw = [];
            $record['_id'] = '' . $item->{$this->repository()->getKeyName()};
            $recordRaw['_id'] = $record['_id'];
            if ($this->engine == GridEngine::TREE) {
                $recordRaw[$treeIdName] = $item->{$treeIdName};
                $recordRaw[$treePidName] = $item->{$treePidName};
                $recordRaw[$treeSortName] = $item->{$treeSortName};
            }
            foreach ($this->listableFields() as $field) {
                /** @var AbstractField $field */
                if ($field->isLayoutField()) {
                    continue;
                }
                $value = null;
                // var_dump($field->column(),$itemColumns);
                if (in_array($field->column(), $itemColumns)
                    || ($item instanceof \Illuminate\Database\Eloquent\Model && method_exists($item, $field->column()))
                ) {
                    // var_dump($field->column());
                    $value = $item->{$field->column()};
                    $field->item($item);
                    if ($field->hookValueUnserialize()) {
                        $value = call_user_func($field->hookValueUnserialize(), $value, $field);
                    }
                    $field->item($item);
                    $value = $field->unserializeValue($value, $field);
                    if ($field->hookFormatValue()) {
                        $value = call_user_func($field->hookFormatValue(), $value, $field);
                    }
                    $item->{$field->column()} = $value;
                } else {
                    $field->item($item);
                    if (str_contains($field->column(), '.')) {
                        $value = ModelUtil::traverse($item, $field->column());
                    }
                    // var_dump($field->column(),$value);
                    if ($field->hookValueUnserialize()) {
                        $value = call_user_func($field->hookValueUnserialize(), $value, $field);
                    }
                    $value = $field->unserializeValue($value, $field);
                    if ($field->hookFormatValue()) {
                        $value = call_user_func($field->hookFormatValue(), $value, $field);
                    }
                    // echo $field->column() . ' - ' . json_encode($value) . "\n";
                }
                if (!in_array($field->column(), ['_operate'])) {
                    $recordRaw[$field->column()] = $value;
                }
                $field->setValue($value);
                // echo $field->column() . ' ' . json_encode($value) . "\n";
                $field->item($item);
                // return $this->repository()->getTreeTitleColumn();
                // echo $field->column() . ' ' . json_encode($value) . "\n";
                $record[$field->column()] = $field->renderView($field, $item, $index);
                // echo $field->column() . ' ' . json_encode($record[$field->column()]) . "\n";
                if ($this->engine == GridEngine::TREE && $field->column() == $this->repository()->getTreeTitleColumn()) {
                    $treePrefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $item->_level - 1)
                        . '<a class="tree-arrow-icon ub-text-muted" href="javascript:;"><i class="icon iconfont icon-angle-right"></i></a> ';
                    $record[$field->column()] = $treePrefix . $record[$field->column()];
                } else if ($this->engine == GridEngine::TREE_MASS && $field->column() == $this->repository()->getTreeTitleColumn()) {
                    if (count($treeAncestors) < $this->treeMaxLevel() - 1) {
                        $url = Request::mergeQueries(['_pid' => $record['_id']]);
                        $record[$field->column()] =
                            '<span class="tree-arrow-icon ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span>'
                            . '<a class="ub-text-primary" href="?' . $url . '" title="' . L('Manage') . '"><i class="icon iconfont icon-sign"></i> ' . htmlspecialchars($record[$field->column()]) . '</a>';
                    } else {
                        $record[$field->column()] =
                            '<span class="tree-arrow-icon ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span>'
                            . $record[$field->column()];
                    }
                }
            }
            if ($this->engine == GridEngine::TREE) {
                $raw['records'][] = $recordRaw;
            }
            // var_dump($record);
            $records[] = $record;
        }
        $head = [];
        foreach ($this->listableFields() as $field) {
            if ($field->isLayoutField()) {
                continue;
            }
            $record = [
                'field' => $field->column(),
                'title' => $field->label(),
                'sort' => $field->sortable(),
            ];
            if ($field->tip()) {
                $record['title'] .= ' <a class="ub-text-muted" href="javascript:;" data-tip-popover="' . htmlspecialchars($field->tip()) . '"><i class="iconfont icon-warning"></i></a>';
            }
            if ($field->width() !== '') {
                $record['width'] = $field->width();
            } else {
                $record['withAuto'] = true;
            }
            if ($field->gridFixed()) {
                $record['fixed'] = $field->gridFixed();
            }
            $head[] = $record;
        }
        $script = null;
        if (!is_null($this->gridRequestScript)) {
            $script = call_user_func($this->gridRequestScript, $this);
        }
        $data = [
            'head' => $head,
            'page' => $paginator ? $paginator->currentPage() : 1,
            'pageSize' => $paginator ? $paginator->perPage() : count($records),
            'total' => $paginator ? $paginator->total() : count($records),
            'records' => $records,
            'addition' => $addition,
            'raw' => $raw,
            'script' => $script,
        ];
        $recordsHtml = null;
        if ($this->hookSimpleRecordsRendering) {
            $data['recordsHtml'] = call_user_func($this->hookSimpleRecordsRendering, $this, $items);
        }
        return Response::generateSuccessData($data);
    }

    public function render()
    {
        $this->build();
        $data = array_merge($this->fluentAttributeVariables(), [
            'id' => $this->id,
            'filters' => $this->gridFilter->filters(),
            'hasAutoHideFilters' => $this->gridFilter->hasAutoHideFilters(),
            'hasVisibleFilters' => $this->gridFilter->hasVisibleFilters(),
            'grid' => $this,
            'scopes' => $this->scopeFilters,
            'gridTableTops' => $this->gridTableTops,
            'gridBeforeRequestScript' => $this->gridBeforeRequestScript,
            'scopeCurrent' => Input::get('_scope', $this->scopeDefault),
            'bodyAppend' => $this->bodyAppend,
        ], $this->viewData);
        return view($this->view, $data)->render();
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return bool
     */
    public function isDynamicModel()
    {
        return $this->isDynamicModel;
    }

    /**
     * @return string
     */
    public function getDynamicModelTableName()
    {
        return $this->dynamicModelTableName;
    }

    /**
     * Generate a Field object and add to form builder if Field exists.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return AbstractField|void|Grid|Form|Detail
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'hookSaved':
            case 'hookDeleting':
            case 'hookChanged':
            case 'hookDeleted':
            case 'hookSaving':
            case 'hookResponse':
            case 'formClass':
            case 'gridFilter':
            case 'sortAddPosition':
                return $this;
        }
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        return FieldManager::call($this, $method, $arguments);
    }
}
