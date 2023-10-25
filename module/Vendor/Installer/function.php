<?php

define('APP_PATH', realpath(__DIR__ . '/../../../'));

if (file_exists($f = APP_PATH . '/app/Constant/AppConstant.php')) {
    include $f;
}

include APP_PATH . '/vendor/modstart/modstart/src/Data/FileManager.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Env/EnvUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/EnvUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/RandomUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/CurlUtil.php';
include APP_PATH . '/vendor/modstart/modstart/src/Core/Util/FileUtil.php';

define('INSTALL_LOCK_FILE', APP_PATH . '/storage/install.lock');
define('ENV_FILE_EXAMPLE', APP_PATH . '/env.example');
define('ENV_FILE', APP_PATH . '/.env');
if (file_exists($licenseFile = APP_PATH . '/license_url.txt')) {
    define('LICENSE_URL', trim(file_get_contents($licenseFile)));
}
if (file_exists($demoData = APP_PATH . '/public/data_demo/data.php')) {
    define('DEMO_DATA', true);
}

if (class_exists(\App\Constant\AppConstant::class)) {
    if (defined('\\App\\Constant\\AppConstant::APP')) {
        define('INSTALL_APP', strtoupper(\App\Constant\AppConstant::APP));
    }
    if (defined('\\App\\Constant\\AppConstant::APP_NAME')) {
        define('INSTALL_APP_NAME', \App\Constant\AppConstant::APP_NAME);
    }
    if (defined('\\App\\Constant\\AppConstant::VERSION')) {
        define('INSTALL_APP_VERSION', \App\Constant\AppConstant::VERSION);
    }
}
if (!defined('INSTALL_APP')) {
    define('INSTALL_APP', 'APP');
}
if (!defined('INSTALL_APP_NAME')) {
    if (defined('INSTALL_APP')) {
        define('INSTALL_APP_NAME', INSTALL_APP);
    } else {
        define('INSTALL_APP_NAME', 'APP');
    }
}
if (!defined('INSTALL_APP_VERSION')) {
    define('INSTALL_APP_VERSION', '0.0.0');
}

if (!file_exists(ENV_FILE)) {
    file_put_contents(ENV_FILE, "APP_ENV=beta\nAPP_DEBUG=true\nAPP_KEY=" . \ModStart\Core\Util\RandomUtil::string(32));
}

function php_is_laravel9()
{
    return in_array(INSTALL_APP, ['CMS9', 'BLOG9']);
}

function php_version_requires()
{
    if (php_is_laravel9()) {
        return join('，', [
            '8.1.x',
        ]);
    }
    return join(', ', [
        '5.6.x',
        '7.0.x',
    ]);
}

function php_version_ok()
{
    if (php_is_laravel9()) {
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            return false;
        }
        return true;
    }
    if (version_compare(PHP_VERSION, '5.5.9', '<')) {
        return false;
    }
    if (version_compare(PHP_VERSION, '7.1.0', '>=')) {
        return false;
    }
    return true;
}

function get_env_config($key, $default = '')
{
    static $envConfig = null;
    if (null == $envConfig) {
        $envConfig = array();
        if (!empty($configFiles = glob(APP_PATH . '/env.*.json'))) {
            foreach ($configFiles as $configFile) {
                $env = @json_decode(@file_get_contents($configFile), true);
                if (!empty($env)) {
                    $envConfig = array_merge($envConfig, $env);
                }
            }
        }
    }
    if (isset($envConfig[$key])) {
        return $envConfig[$key];
    }
    $osEnvMap = [
        'db_host' => ['DB_HOST', 'MYSQL_HOST'],
        'db_port' => ['DB_DATABASE', 'MYSQL_PORT'],
        'db_name' => ['DB_DATABASE', 'MYSQL_DB'],
        'db_username' => ['DB_USERNAME', 'MYSQL_USER'],
        'db_password' => ['DB_PASSWORD', 'MYSQL_PASSWORD'],
        'db_prefix' => ['DB_PREFIX'],
        'admin_username' => ['ADMIN_USERNAME'],
        'admin_password' => ['ADMIN_PASSWORD'],
    ];
    if (isset($osEnvMap[$key])) {
        foreach ($osEnvMap[$key] as $k) {
            $v = @getenv($k);
            if (!empty($v)) {
                return $v;
            }
        }
    }
    $envMap = [
        'db_host' => 'DB_HOST',
        'db_port' => 'DB_PORT',
        'db_name' => 'DB_DATABASE',
        'db_username' => 'DB_USERNAME',
        'db_password' => 'DB_PASSWORD',
        'db_prefix' => 'DB_PREFIX',
        'admin_username' => 'ADMIN_USERNAME',
        'admin_password' => 'ADMIN_PASSWORD',
    ];
    if (isset($envMap[$key])) {
        $v = env($envMap[$key], null);
        if (!is_null($v)) {
            return $v;
        }
    }
    return $default;
}

function response_json_error_quit($msg)
{
    response_json_quit(-1, $msg);
}

function response_json_quit($code, $msg, $data = null, $redirect = null)
{
    header('Content-type: application/json');
    echo json_encode([
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
        'redirect' => $redirect,
    ]);
    exit();
}

function request_schema()
{
    $schema = 'http';
    if (is_https()) {
        $schema = 'https';
    }
    return $schema;
}

function request_domain()
{
    return $_SERVER['HTTP_HOST'];
}

function request_url()
{
    return request_schema() . '://' . request_domain();
}

function request_is_post()
{
    return !empty($_POST);
}

function is_https()
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

function request_post($k, $defaultValue = '')
{
    return isset($_POST[$k]) ? $_POST[$k] : $defaultValue;
}

function text_success($msg)
{
    echo '<div class="ub-alert success">√ ' . $msg . '</div>';
}

function text_error($msg, $solutionUrl = null, $count = true)
{
    if ($count) {
        error_counter();
    }
    echo '<div class="ub-alert danger">× ' . $msg . ' ' . ($solutionUrl ? '<a target="_blank" href="' . $solutionUrl . '">解决办法</a>' : '') . '</div>';
}

function text_warning($msg, $solutionUrl = null)
{
    echo '<div class="ub-alert warning">? ' . $msg . ' ' . ($solutionUrl ? '<a target="_blank" href="' . $solutionUrl . '">解决办法</a>' : '') . '</div>';
}

function error_counter($inc = 1)
{
    static $error = 0;
    $error += $inc;
    return $error;
}

function env_writable()
{
    $file = APP_PATH . '/.env';
    if (!file_exists($file)) {
        if (false === file_put_contents($file, "")) {
            @unlink($file);
            return false;
        }
        @unlink($file);
        return true;
    }
    $content = @file_get_contents($file);
    if (false === file_put_contents($file, $content)) {
        return false;
    }
    return true;
}

function rewrite_check()
{
    if (file_exists(APP_PATH . '/storage/rewrite.skip')) {
        return ['code' => 0, 'msg' => 'ok'];
    }
    $domain = request_domain();
    $url = request_url() . '/install/ping';
    $ret = \ModStart\Core\Util\CurlUtil::get($url, [], [
        'timeout' => 3,
    ]);
    if ($ret['body'] === 'ok') {
        return ['code' => 0, 'msg' => ''];
    }
    $msgs = [];
    if (!empty($ret['error'])) {
        if (false !== strpos($ret['error'], 'Resolving timed out')) {
            $msgs[] = "- 域名 $domain 解析失败（可能您没有解析域名）";
            $msgs[] = "- 在服务器不能访问 $url ，需要在程序中通过改地址访问到程序";
        } else {
            $msgs[] = '- ERROR:' . $ret['error'];
        }
    }
    if (!empty($ret['code'])) {
        if (!empty($ret['body'])) {
            $msg = $ret['body'];
            $index = strpos($ret['body'], '<body>');
            if (false !== $index) {
                $msg = substr($msg, $index);
            }
            $index = strpos($ret['body'], '</body>');
            if (false !== $index) {
                $msg = substr($msg, 0, $index);
            }
            $msgs[] = '- 程序出错:' . preg_replace('/\\s+/', ' ', preg_replace('/<[^>]+>/', '', $msg));
        }
    }
    $msgs[] = "- 您还可以在浏览器访问 <a href='$url' target='_blank'>$url</a> 查看报错信息，调整配置保证测试页面提示“ok”字样";
    return ['code' => -1, 'msg' => 'Rewrite规则错误可能原因（仅供参考）：<br/>' . join('<br/>', $msgs)];
}

function env($key, $defaultValue = '')
{
    static $values = null;
    if (null === $values) {
        $values = \ModStart\Core\Util\EnvUtil::all(ENV_FILE_EXAMPLE);
    }
    return isset($values[$key]) ? $values[$key] : $defaultValue;
}

function is_dir_really_writable($dir)
{
    $dir = rtrim($dir, '/\\') . '/';
    $testFile = $dir . '.writable_test_file';
    @file_put_contents($testFile, 'test');
    if (!file_exists($testFile)) {
        return false;
    }
    if (file_get_contents($testFile) != 'test') {
        return false;
    }
    @unlink($testFile);
    return true;
}
