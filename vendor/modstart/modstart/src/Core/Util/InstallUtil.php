<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\DB;
use ModStart\Core\Dao\ModelUtil;

/**
 * 安装工具类
 * 提供 ModStart 初始化安装相关的公共逻辑（如演示数据导入），
 * 供命令行安装（modstart:init）与 Web 安装向导（InstallController）复用。
 */
class InstallUtil
{
    /**
     * 安装演示数据
     * 读取 public/data_demo/data.php，导入其中的 inserts 与 updates，
     * 逻辑与 Web 安装向导 InstallController::execute 的演示数据部分一致。
     *
     * @param callable|null $logCallback 可选日志回调，签名 function($msg)
     * @return array ['success'=>bool, 'msg'=>string, 'insertCount'=>int]
     */
    public static function installDemoData($logCallback = null)
    {
        $file = public_path('data_demo/data.php');
        if (!file_exists($file)) {
            return ['success' => true, 'msg' => '演示数据文件不存在', 'insertCount' => 0];
        }
        $data = include($file);
        if (empty($data) || !is_array($data)) {
            return ['success' => false, 'msg' => '演示数据文件格式错误', 'insertCount' => 0];
        }
        $insertCount = 0;
        if (!empty($data['inserts']) && is_array($data['inserts'])) {
            foreach ($data['inserts'] as $table => $records) {
                if (empty($records) || !is_array($records)) {
                    continue;
                }
                ModelUtil::insertAll($table, $records);
                $insertCount += count($records);
                self::log($logCallback, "insert {$table} " . count($records) . ' rows');
            }
        }
        if (!empty($data['updates']) && is_array($data['updates'])) {
            foreach ($data['updates'] as $record) {
                if (empty($record['table']) || !isset($record['update'])) {
                    continue;
                }
                $query = DB::table($record['table']);
                if (!empty($record['where']) && is_array($record['where'])) {
                    $query->where($record['where']);
                }
                $query->update($record['update']);
                self::log($logCallback, "update {$record['table']}");
            }
        }
        return ['success' => true, 'msg' => 'ok', 'insertCount' => $insertCount];
    }

    private static function log($callback, $msg)
    {
        if (is_callable($callback)) {
            call_user_func($callback, $msg);
        }
    }
}