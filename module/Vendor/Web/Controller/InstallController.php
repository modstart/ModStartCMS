<?php


namespace Module\Vendor\Web\Controller;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ModStart\Admin\Auth\Admin;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\RandomUtil;
use ModStart\Module\ModuleManager;
use PDO;

class InstallController extends Controller
{
    public function lock()
    {
        $installLockFile = storage_path('install.lock');
        if (file_exists($installLockFile)) {
            return Response::send(0, 'install lock error -_-');
        }
        file_put_contents($installLockFile, 'lock');
        return Response::send(0, 'install lock ok ^_^');
    }

    public function ping()
    {
        try {
            $exitCode = Artisan::call("env");
            $output = trim(Artisan::output());
            if (0 == $exitCode) {
                if (Str::contains($output, 'Current application environment')) {
                    return 'ok';
                }
                if (Str::contains($output, 'The application environment is')) {
                    return 'ok';
                }
            }
            return 'ERROR: code=' . $exitCode . ', msg:' . $output;
        } catch (\Exception $e) {
            return 'ERROR:' . $e->getMessage();
        }
    }

    public function prepare()
    {
        if (file_exists(storage_path('install.lock'))) {
            return Response::jsonError("系统不能重复安装（请删除install.lock文件后重试）");
        }
        if (file_exists($p = base_path('.env'))) {
            $content = file_get_contents($p);
            if (str_contains($content, 'DB_HOST')) {
                return Response::jsonError('请先清空.env文件');
            }
        }

        $input = InputPackage::buildFromInput();
        $dbHost = $input->getTrimString('db_host');
        $dbPort = $input->getTrimString('db_port');
        $dbDatabase = $input->getTrimString('db_database');
        $dbUsername = $input->getTrimString('db_username');
        $dbPassword = $input->getTrimString('db_password', '');
        $dbPrefix = $input->getTrimString('db_prefix', '');
        $username = $input->getTrimString('username');
        $password = $input->getTrimString('password');
        $installDemo = $input->getBoolean('installDemo');
        $installLicense = $input->getBoolean('installLicense');
        $installConfig = $input->getJson('INSTALL_CONFIG');
        if (empty($dbHost)) {
            return Response::jsonError("数据库主机名不能为空");
        }
        if (empty($dbPort)) {
            return Response::jsonError("数据库端口不能为空");
        }
        if (empty($dbDatabase)) {
            return Response::jsonError("数据库数据库不能为空");
        }
        if (empty($dbUsername)) {
            return Response::jsonError("数据库用户名不能为空");
        }
        if (empty($username)) {
            return Response::jsonError("管理用户不能为空");
        }
        if (empty($password)) {
            return Response::jsonError("管理用户密码不能为空");
        }
        if (file_exists(base_path('license_url.txt')) && !$installLicense) {
            return Response::jsonError("请先同意《软件安装许可协议》");
        }

        // 数据库连接检测
        try {
            new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbDatabase", $dbUsername, $dbPassword);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'Server sent charset unknown to the client')) {
                return Response::generateError('数据库编码不支持：' . $msg);
            } else if (str_contains($msg, 'Access denied for user')) {
                return Response::generateError('用户密码不匹配：' . $msg);
            }
            Log::error('InstallError -> ' . $e->getMessage() . ' -> ' . $e->getTraceAsString());
            return Response::jsonError('连接数据信息 ' . $dbHost . ':' . $dbPort . '.' . $dbDatabase . ' 失败!');
        }

        // 替换.env文件
        $envContent = file_get_contents(base_path('env.example'));

        $envContent = preg_replace("/APP_DEBUG=(.*?)\\n/", "APP_DEBUG=false\n", $envContent);
        $envContent = preg_replace("/DB_HOST=(.*?)\\n/", "DB_HOST=" . $dbHost . "\n", $envContent);
        $envContent = preg_replace("/DB_PORT=(.*?)\\n/", "DB_PORT=" . $dbPort . "\n", $envContent);
        $envContent = preg_replace("/DB_DATABASE=(.*?)\\n/", "DB_DATABASE=" . $dbDatabase . "\n", $envContent);
        $envContent = preg_replace("/DB_USERNAME=(.*?)\\n/", "DB_USERNAME=" . $dbUsername . "\n", $envContent);
        $envContent = preg_replace("/DB_PASSWORD=(.*?)\\n/", "DB_PASSWORD=" . $dbPassword . "\n", $envContent);
        $envContent = preg_replace("/DB_PREFIX=(.*?)\\n/", "DB_PREFIX=" . $dbPrefix . "\n", $envContent);
        $envContent = preg_replace("/APP_KEY=(.*?)\\n/", "APP_KEY=" . RandomUtil::string(32) . "\n", $envContent);
        $envContent = preg_replace("/ENCRYPT_KEY=(.*?)\\n/", "ENCRYPT_KEY=" . RandomUtil::string(32) . "\n", $envContent);
        if (!empty($installConfig['envs'])) {
            foreach ($installConfig['envs'] as $envField) {
                $envContent = preg_replace(
                    "/" . $envField['name'] . "=(.*?)\\n/",
                    $envField['name'] . "=" . $input->getTrimString($envField['name']) . "\n",
                    $envContent
                );
            }
        }

        file_put_contents(base_path('.env'), $envContent);

        return Response::jsonSuccess();
    }

    public function execute()
    {
        if (file_exists(storage_path('install.lock'))) {
            return Response::jsonError("系统不能重复安装（请删除install.lock文件后重试）");
        }
        $input = InputPackage::buildFromInput();
        $username = $input->getTrimString("username");
        $password = $input->getTrimString("password");
        $installDemo = $input->getBoolean('installDemo');
        if (empty($username)) {
            return Response::jsonError("管理用户名为空");
        }
        if (empty($password)) {
            return Response::jsonError("管理用户密码为空");
        }
        set_time_limit(0);
        $exitCode = 0;
        try {
            $exitCode = Artisan::call("migrate");
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
        if (0 != $exitCode) {
            return Response::jsonError("安装错误 exitCode($exitCode)");
        }

        $adminUserCount = ModelUtil::count('admin_user');
        if ($adminUserCount === 0) {
            Admin::add($username, $password);
        }

        /**
         * 预安装所有模块
         */
        foreach (ModuleManager::listAllInstalledModulesInRequiredOrder() as $module) {
            if (!ModuleManager::isExists($module)) {
                continue;
            }
            try {
                $ret = ModuleManager::install($module);
            } catch (\Exception $e) {
                return $this->handleException($e);
            }
            if (Response::isError($ret)) {
                return Response::generateError($ret['msg']);
            }
        }

        // 初始化数据
        if ($installDemo && file_exists($file = public_path('data_demo/data.php'))) {
            $data = include($file);
            if (!empty($data['inserts'])) {
                foreach ($data['inserts'] as $table => $records) {
                    ModelUtil::insertAll($table, $records);
                }
            }
            if (!empty($data['updates'])) {
                foreach ($data['updates'] as $record) {
                    DB::table($record['table'])->where($record['where'])->update($record['update']);
                }
            }
        }

        file_put_contents(storage_path('install.lock'), 'lock');

        return Response::json(0, '安装成功，点击即将跳转到管理后台', null, '/admin');
    }

    private function handleException(\Exception $e)
    {
        $msg = $e->getMessage();
        $traces = $e->getTraceAsString();
        if (preg_match("/Table '(.*?)' already exists/", $msg, $mat)) {
            return Response::jsonError('数据表 ' . $mat[1] . ' 已经存在（可能您使用了一个非空的数据库，请删除表或更新数据库）');
        }
        if (preg_match("/Duplicate column name '(.*?)'/", $msg, $mat)) {
            $field = $mat[1];
            $file = null;
            if (preg_match("/(module\\/.*?\\/Migrate\\/.*?\\.php)/", $traces, $mat)) {
                $file = $mat[1];
            }
            return Response::jsonError('数据表字段 ' . $field . ' 已经存在（请查看' . $file . '迁移文件配置）');
        }
        throw $e;
    }
}
