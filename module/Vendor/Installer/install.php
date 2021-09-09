<?php



use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\RandomUtil;

include __DIR__ . '/function.php';
?>
<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <script src="/asset/vendor/jquery.js"></script>
    <script src="/asset/common/base.js"></script>
    <script src="/asset/layui/layui.js"></script>
    <link rel="stylesheet" href="/asset/layui/css/layui.css">
    <link rel="stylesheet" href="/asset/theme/default/style.css">
    <title>安装助手</title>
    <style type="text/css">
        body, html {
            min-height: 100%;
        }

        .license-content p {
            font-size: 14px;
            line-height: 1.8em;
            margin: 0;
        }
    </style>
</head>
<body style="background:#333;padding:40px 0;">
<div style="width:600px;min-height:100vh;margin:0 auto;">

    <?php if (file_exists(APP_PATH . '/storage/install.lock')) { ?>
        <div class="ub-alert ub-alert-danger ub-text-center">系统无需重复安装</div>
    <?php } else { ?>
        <h1 class="ub-text-center" style="color:#FFF;">
            安装助手
        </h1>
        <div class="ub-panel">
            <div class="head">
                <div class="title">环境检查</div>
            </div>
            <div class="body">
                <?php text_success('系统：' . PHP_OS); ?>
                <?php version_compare(PHP_VERSION, '5.5.9', '>=') ? text_success('PHP版本' . PHP_VERSION) : text_error('PHP版本>=5.5.9 当前为' . PHP_VERSION); ?>
                <?php text_success('最大上传：' . FileUtil::formatByte(\ModStart\Core\Util\EnvUtil::env('uploadMaxSize'))); ?>
                <?php function_exists('openssl_open') ? text_success('OpenSSL PHP 扩展') : text_error('缺少 OpenSSL PHP 扩展'); ?>
                <?php function_exists('exif_read_data') ? text_success('Exif PHP 扩展') : text_error('缺少 Exif PHP 扩展'); ?>
                <?php function_exists('proc_open') ? text_success('proc_open 函数') : text_error('缺少 proc_open 函数'); ?>
                <?php function_exists('putenv') ? text_success('putenv 函数') : text_error('缺少 putenv 函数'); ?>
                <?php function_exists('proc_get_status') ? text_success('proc_get_status 函数') : text_error('缺少 proc_get_status 函数'); ?>
                <?php function_exists('bcmul') ? text_success('bcmath 扩展') : text_error('缺少 PHP bcmath 扩展'); ?>
                <?php class_exists('pdo') ? text_success('PDO PHP 扩展') : text_error('缺少 PDO PHP 扩展'); ?>
                <?php (class_exists('pdo') && in_array('mysql', PDO::getAvailableDrivers())) ? text_success('PDO Mysql 驱动正常') : text_error('缺少 PDO Mysql 驱动'); ?>
                <?php function_exists('mb_internal_encoding') ? text_success('缺少 Mbstring PHP 扩展') : text_error('Mbstring PHP 扩展'); ?>
                <?php function_exists('token_get_all') ? text_success('缺少 Tokenizer PHP 扩展') : text_error('Tokenizer PHP 扩展'); ?>
                <?php function_exists('finfo_file') ? text_success('缺少 PHP Fileinfo 扩展') : text_error('PHP Fileinfo 扩展'); ?>
                <?php is_writable(APP_PATH . '/storage/') ? text_success('/storage/目录可写') : text_error('/storage/目录不可写'); ?>
                <?php is_writable(APP_PATH . '/public/') ? text_success('/public/目录可写') : text_error('/public/目录不可写'); ?>
                <?php is_writable(APP_PATH . '/bootstrap/cache/') ? text_success('/bootstrap/cache/目录可写') : text_error('/bootstrap/cache/目录不可写'); ?>
                <div data-rewrite-check>
                    <div class="status loading"><div class="ub-alert">Rewrite规则检测中...</div></div>
                    <div class="status success" style="display:none;"><?php echo text_success('Rewrite规则正确'); ?></div>
                    <div class="status error" style="display:none;"><?php echo text_error('Rewrite规则错误',null,false); ?></div>
                    <div class="status error ub-alert ub-alert-warning" style="display:none;">
                        <div>- 配置Nginx/Apache，保证访问 /install/ping 出现 ok 字样。</div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (error_counter(0) > 0) { ?>
            <div class="ub-alert ub-alert-danger ub-text-center">请解决以上 <?php echo error_counter(0); ?> 个问题再进行安装</div>
        <?php } else if (!env_writable()) { ?>
            <div class="ub-alert ub-alert-danger ub-text-center">/.env文件不可写，请手动配置安装</div>
        <?php } else { ?>
            <div style="display:none;" class="ub-form ub-form-stacked">
                <div class="ub-panel" style="background:#FFF;">
                    <div class="head">
                        <div class="title">MySQL数据库信息</div>
                    </div>
                    <div class="body">
                        <div class="line">
                            <label class="label"><span class="ub-text-danger">*</span> 主机</label>
                            <input type="text" style="width:100%;" name="db_host"
                                   value="<?php echo htmlspecialchars(get_env_config('db_host')); ?>"/>
                        </div>
                        <div class="line">
                            <label class="label"><span class="ub-text-danger">*</span> 数据库名</label>
                            <input type="text" style="width:100%;" name="db_database"
                                   value="<?php echo htmlspecialchars(get_env_config('db_name')); ?>"/>
                        </div>
                        <div class="line">
                            <label class="label"><span class="ub-text-danger">*</span> 用户名</label>
                            <input type="text" style="width:100%;" name="db_username"
                                   value="<?php echo htmlspecialchars(get_env_config('db_username')); ?>"/>
                        </div>
                        <div class="line">
                            <label class="label"><span class="ub-text-danger">*</span> 密码</label>
                            <input type="text" style="width:100%;" name="db_password"
                                   value="<?php echo htmlspecialchars(get_env_config('db_password')); ?>"/>
                        </div>
                        <div class="line">
                            <label class="label">数据表前缀</label>
                            <input type="text" style="width:100%;" name="db_prefix"
                                   value="<?php echo htmlspecialchars(get_env_config('db_prefix')); ?>"/>
                        </div>
                    </div>
                </div>
                <?php if(!empty($INSTALL_CONFIG)){ ?>
                    <div class="ub-panel">
                        <div class="head">
                            <div class="title">系统配置</div>
                        </div>
                        <div class="body">
                            <?php if(!empty($INSTALL_CONFIG['envs'])){ ?>
                                <?php foreach($INSTALL_CONFIG['envs'] as $envField){ ?>
                                    <div class="line">
                                        <div class="label"><span class="ub-text-danger">*</span> <?php echo htmlspecialchars($envField['label']); ?></div>
                                        <div class="field">
                                            <input class="form" type="text" name="<?php echo $envField['name']; ?>" value="<?php echo htmlspecialchars($envField['default']); ?>" />
                                            <div class="help"><?php echo $envField['desc']; ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">管理信息</div>
                    </div>
                    <div class="body">
                        <div class="line">
                            <label class="label"><span class="ub-text-danger">*</span> 用户</label>
                            <input type="text" style="width:100%;" name="username"
                                   value="<?php echo htmlspecialchars(get_env_config('admin_username')); ?>"/>
                        </div>
                        <div class="line">
                            <label class="label"><span class="ub-text-danger">*</span> 密码</label>
                            <input type="text" style="width:100%;" name="password"
                                   placeholder="<?php echo htmlspecialchars(get_env_config('admin_password')); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="ub-panel" style="margin-top:20px;">
                    <div class="head">
                        <div class="title">
                            安装操作
                        </div>
                    </div>
                    <div class="body">
                        <?php if(defined('DEMO_DATA') || defined('LICENSE_URL')) { ?>
                            <div class="line">
                                <div class="field">
                                    <?php if (defined('DEMO_DATA')) { ?>
                                        <div>
                                            <label style="border:none;margin-top:-10px;">
                                                <input type="checkbox" name="installDemo" value="1"/>
                                                安装演示数据
                                            </label>
                                        </div>
                                    <?php } ?>
                                    <?php if (defined('LICENSE_URL')) { ?>
                                        <div>
                                            <label style="border:none;margin-top:-10px;margin-right:0;padding-right:0;">
                                                <input type="checkbox" name="installLicense" value="1"/>
                                                同意
                                            </label>
                                            <a href="<?php echo LICENSE_URL; ?>" target="_blank">《软件安装许可协议》</a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div style="text-align:center;">
                            <input type="hidden" name="INSTALL_CONFIG" value="<?php echo htmlspecialchars(json_encode(isset($INSTALL_CONFIG)?$INSTALL_CONFIG:null)); ?>" />
                            <button type="button" onclick="doSubmit();" style="width:40%;" class="btn btn-primary">提交</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
<script>
    $(function () {
        var $rewriteCheck = $('[data-rewrite-check]');
        $.ajax({
            url: '/install/ping',
            type: 'GET',
            success: function(data){
                if('ok'===data){
                    $rewriteCheck.find('.status').hide().filter('.success').show();
                    $('.ub-form').show();
                }else{
                    $rewriteCheck.find('.status').hide().filter('.error').show();
                }
            },
            error: function(data) {
                $rewriteCheck.find('.status').hide().filter('.error').show();
            }
        });
        window.doSubmit = function(){
            var data = {};
            var $form = $('.ub-form');
            $form.find('input[type=text],input[type=hidden]').each(function(i,o){
                data[$(o).attr('name')] = $(o).val();
            });
            $form.find('input[type=checkbox]:checked').each(function(i,o){
                data[$(o).attr('name')] = $(o).val();
            });
            window.api.dialog.loadingOn('正在提交表单..');
            window.api.base.post('/install/prepare',data,function(res){
                window.api.dialog.loadingOff();
                window.api.base.defaultFormCallback(res, {
                    success: function (res) {
                        window.api.dialog.loadingOn('正在安装系统，可能需要较长时间，请耐心等待...');
                        window.api.base.post('/install/execute',data,function(res){
                            window.api.dialog.loadingOff();
                            window.api.base.defaultFormCallback(res);
                        });
                    }
                })
            });
            return false;
        };
    });
</script>
</body>
</html>
