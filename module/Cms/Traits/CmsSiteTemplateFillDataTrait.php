<?php


namespace Module\Cms\Traits;

use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ReUtil;
use ModStart\Form\Form;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsModelUtil;

trait CmsSiteTemplateFillDataTrait
{
    public function fillData(AdminConfigBuilder $builder)
    {
        $module = ReUtil::group1('/Module\\\\(.*?)\\\\/', __CLASS__);
        BizException::throwsIfEmpty('识别到模块为空', $module);
        $initDataFile = ModuleManager::path($module, 'demo_data.php');
        BizException::throwsIf('演示文件不存在（' . $module . '/demo_data.php）', !file_exists($initDataFile));
        $demoData = (include $initDataFile);
        $builder->display('_display', '')->content('<div class="ub-alert warning"><i class="iconfont icon-warning"></i> 初始化信息前清空对应的信息记录</div>')->addable(true);
        $recordFields = [
            'banner' => '轮播',
            'news' => '新闻资讯',
            'product' => '产品',
            'cases' => '案例',
            'job' => '招聘',
            'nav' => '导航',
            'info' => '基础信息',
        ];
        foreach ($recordFields as $field => $title) {
            if (isset($demoData['tables'][$field])) {
                $builder->switch($field, "初始化$title")->optionsYesNo()->defaultValue(true);
            }
        }
        $builder->formClass('wide');
        $builder->useDialog();
        $builder->pageTitle('初始化演示数据');
        return $builder->perform(false, function (Form $form) use ($recordFields, $demoData) {
            AdminPermission::demoCheck();
            $data = $form->dataForming();
            $filterRecordFields = [];
            foreach ($recordFields as $field => $title) {
                if (empty($demoData['tables'][$field]) || empty($data[$field])) {
                    continue;
                }
                if (empty($demoData['tables'][$field]['records'])) {
                    continue;
                }
                if (empty($demoData['tables'][$field]['where'])) {
                    $demoData['tables'][$field]['where'] = [];
                }
                $filterRecordFields[$field] = $title;
            }
            foreach ($filterRecordFields as $field => $title) {
                $recordInfo = $demoData['tables'][$field];
                switch ($field) {
                    case 'product':
                    case 'cases':
                    case 'job':
                    case 'news':
                        $model = CmsModelUtil::getByName($field);
                        $cat = CmsCatUtil::getByUrl($field);
                        BizException::throwsIfEmpty($title . "栏目不存在（需要设置访问路径为 $field ）", $cat);
                        $where = array_merge(
                            ['modelId' => $model['id']],
                            $recordInfo['where']
                        );
                        BizException::throwsIf($title . '数据表不为空', ModelUtil::count("cms_content", $where) > 0);
                        break;
                    case 'banner':
                    case 'nav':
                        BizException::throwsIf($title . '数据表不为空', ModelUtil::count($field, $recordInfo['where']) > 0);
                        break;
                    case 'info':
                        break;
                }
            }
            ModelUtil::transactionBegin();
            foreach ($filterRecordFields as $field => $title) {
                $recordInfo = $demoData['tables'][$field];
                foreach ($recordInfo['records'] as $k => $record) {
                    switch ($field) {
                        case 'product':
                        case 'cases':
                        case 'job':
                        case 'news':
                            $model = CmsModelUtil::getByName($field);
                            $cat = CmsCatUtil::getByUrl($field);
                            $data = array_merge([
                                'modelId' => $model['id'],
                                'catId' => $cat['id'],
                            ], $record);
                            if (empty($data['_data'])) {
                                $data['_data'] = [
                                    'content' => ''
                                ];
                            }
                            $dataData = $data['_data'];
                            unset($data['_data']);
                            if (!isset($data['status'])) {
                                $data['status'] = CmsModelContentStatus::SHOW;
                            }
                            if (!isset($data['postTime'])) {
                                $data['postTime'] = date('Y-m-d H:i:s', strtotime('2021-01-01 00:00:00') + $k * 10);
                            }
                            if (!isset($data['isRecommend'])) {
                                $data['isRecommend'] = true;
                            }
                            if (!ModelUtil::exists('cms_content', $data)) {
                                CmsContentUtil::insert($model, $data, $dataData);
                            }
                            break;
                        case 'banner':
                        case 'nav':
                            $data = array_merge($recordInfo['where'], $record);
                            if (!ModelUtil::exists($field, $data)) {
                                ModelUtil::insert($field, $data);
                            }
                            break;
                        case 'info':
                            modstart_config()->set($k, $record);
                            break;
                    }
                }
            }
            ModelUtil::transactionCommit();
            ModStart::clearCache();
            return Response::generateSuccess('操作成功');
        });
    }
}
