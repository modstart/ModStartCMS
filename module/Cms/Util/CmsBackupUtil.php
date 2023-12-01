<?php


namespace Module\Cms\Util;


use ModStart\Core\Dao\ModelManageUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
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
                $results[] = [
                    'module' => $theme->name(),
                    'root' => 'module/' . $theme->name() . '/Backup',
                    'filename' => $file['filename'],
                    'size' => $file['size'],
                    'tables' => array_keys($json['structure']),
                    'config' => $json['config'],
                ];
            }
        }
        return $results;
    }

    public static function restoreBackup($backup)
    {
        BizException::throwsIf('备份文件损坏', empty($backup['structure']));
        BizException::throwsIf('备份文件损坏', empty($backup['backup']));
        $tableBackupBatch = '_del_' . date('Ymd_His_');
        foreach ($backup['structure'] as $table => $structure) {
            if (!self::isCmsTable($table)) {
                continue;
            }
            if (ModelManageUtil::hasTable($table)) {
                ModelManageUtil::renameTable($table, $tableBackupBatch . $table);
            }
            $structure = str_replace('__table_prefix__', ModelManageUtil::tablePrefix(), $structure);
            ModelManageUtil::statement($structure);
        }
        foreach ($backup['backup'] as $table => $data) {
            if (!self::isCmsTable($table)) {
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
        return $tables;
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
