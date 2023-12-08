<?php


namespace Module\Cms\Util;


use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Module\ModuleManager;
use Module\Cms\Provider\Theme\CmsThemeProvider;

class CmsBackupUtil
{
    public static function listBackups()
    {
        $results = [];
        foreach (CmsThemeProvider::all() as $theme) {
            $root = ModuleManager::path($theme->name(), 'Backup/');
            $files = FileUtil::listFiles($root, '*.json');
            foreach ($files as $file) {
                $content = file_get_contents($file['pathname']);
                if (empty($content)) {
                    continue;
                }
                $json = @json_decode($content, true);
                if (empty($json) || empty($json['structure'])) {
                    continue;
                }
                if (empty($json['config'])) {
                    $json['config'] = [];
                }
                $moduleInfo = ModuleManager::getModuleBasic($theme->name());
                $results[] = [
                    'module' => $theme->name(),
                    'moduleInfo' => $moduleInfo,
                    'root' => 'module/' . $theme->name() . '/Backup',
                    'filename' => $file['filename'],
                    'filemtime' => filemtime($file['pathname']),
                    'size' => $file['size'],
                    'tables' => array_keys($json['structure']),
                    'config' => $json['config'],
                ];
            }
        }
        $results = ArrayUtil::sortByKey($results, 'filemtime', 'desc');
        return $results;
    }

    public static function restoreBackup($backup)
    {
        BizException::throwsIf('备份文件损坏', empty($backup['structure']));
        BizException::throwsIf('备份文件损坏', empty($backup['backup']));
        $tableBackupBatch = '_del_' . date('Ymd_His_');
        foreach ($backup['structure'] as $table => $structure) {
            if (!self::isCmsTable($table) && !self::isCmsBackupTable($table)) {
                continue;
            }
            if (ModelManageUtil::hasTable($table)) {
                ModelManageUtil::renameTable($table, $tableBackupBatch . $table);
            }
            $structure = str_replace('__table_prefix__', ModelManageUtil::tablePrefix(), $structure);
            ModelManageUtil::statement($structure);
        }
        foreach ($backup['backup'] as $table => $data) {
            if (!self::isCmsTable($table) && !self::isCmsBackupTable($table)) {
                continue;
            }
            ModelUtil::insertAll($table, $data, false);
        }
        if (!empty($backup['config'])) {
            $config = modstart_config();
            foreach ($backup['config'] as $k => $v) {
                $config->set($k, $v);
            }
        }
    }

    public static function mergeConfigTitle(&$configs)
    {
        $titleMap = [
            '/^Cms_/' => 'CMS配置',
            '/^CmsTheme.*?/' => '主题配置',
        ];
        foreach ($configs as $k => $v) {
            foreach ($titleMap as $pattern => $title) {
                if (preg_match($pattern, $v['key'])) {
                    $configs[$k]['title'] = $title;
                    break;
                }
            }
        }
    }

    public static function mergeTableTitle(&$tables)
    {
        $titleMap = [
            '/^banner$/' => '通用轮播',
            '/^partner$/' => '友情链接',
            '/^nav$/' => '通用导航',
            '/^content_block$/' => '内容区块',
            '/^cms_model$/' => 'CMS模型',
            '/^cms_model_field$/' => 'CMS模型字段',
            '/^cms_cat$/' => 'CMS栏目',
            '/^cms_content$/' => 'CMS内容主表',
            '/^cms_m_.*?$/' => 'CMS内容副表',
        ];
        foreach ($tables as $k => $v) {
            foreach ($titleMap as $pattern => $title) {
                if (preg_match($pattern, $v['name'])) {
                    $tables[$k]['title'] = $title;
                    break;
                }
            }
        }
    }

    public static function listBackupConfigs()
    {
        $configs = modstart_config()->all();
        $buildIns = [
            '/^Cms_/',
            '/^CmsTheme.*?/',
        ];
        $configs = array_filter($configs, function ($o) use ($buildIns) {
            foreach ($buildIns as $pattern) {
                if (preg_match($pattern, $o['key'])) {
                    return true;
                }
            }
            return false;
        });
        CmsBackupUtil::mergeConfigTitle($configs);
        $configs = ArrayUtil::sortByKey($configs, 'key');
        return array_values($configs);
    }

    public static function listBackupTables()
    {
        $tables = ModelManageUtil::listTables();
        $tables = array_filter($tables, function ($table) {
            return self::isCmsTable($table);
        });
        $tables = array_map(function ($table) {
            return [
                'name' => $table,
                'checked' => true,
            ];
        }, $tables);
        foreach ([
                     'Banner' => ['banner'],
                     'Partner' => ['partner'],
                     'Nav' => ['nav'],
                     'ContentBlock' => ['content_block'],
                 ] as $module => $moduleTables) {
            if (!modstart_module_enabled($module)) {
                continue;
            }
            foreach ($moduleTables as $moduleTable) {
                $tables[] = [
                    'name' => $moduleTable,
                    'checked' => false,
                ];
            }
        }
        self::mergeTableTitle($tables);
        $tables = ArrayUtil::sortByKey($tables, 'name');
        return array_values($tables);
    }

    public static function isCmsBackupTable($table)
    {
        $tables = [
            'banner',
            'partner',
            'nav',
            'content_block',
        ];
        return in_array($table, $tables);
    }

    public static function isCmsTable($table)
    {
        if (in_array($table, ['cms_cat', 'cms_content', 'cms_model', 'cms_model_field'])) {
            return true;
        }
        if (starts_with($table, 'cms_m_')) {
            return true;
        }
        return false;
    }
}
