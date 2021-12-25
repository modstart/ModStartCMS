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
                $results[] = [
                    'module' => $theme->name(),
                    'root' => 'module/' . $theme->name() . '/Backup',
                    'filename' => $file['filename'],
                    'size' => $file['size'],
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
    }

    public static function listBackupTables()
    {
        $tables = ModelManageUtil::listTables();
        $tables = array_filter($tables, function ($table) {
            return self::isCmsTable($table);
        });
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