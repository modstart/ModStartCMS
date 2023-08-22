<?php

//$INSTALL_CONFIG = [
//    'envs' => [
//        [
//            'name' => 'DOMAIN_MAIN',
//            'label' => '主域名',
//            'desc' => '如期望不同城市的访问域名为 bj.example.com, sh.example.com 需要配置主域名为 example.com',
//            'default' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '',
//        ],
//    ]
//];

use ModStart\Core\Util\EnvUtil;
use ModStart\Core\Util\FileUtil;

include __DIR__ . '/function.php';
?>
<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <script src="/asset/vendor/jquery.js?<?php echo time(); ?>"></script>
    <script src="/asset/common/base.js?<?php echo time(); ?>"></script>
    <script src="/asset/layui/layui.js?<?php echo time(); ?>"></script>
    <link rel="stylesheet" href="/asset/vendor/iconfont/iconfont.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="/asset/layui/css/layui.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="/asset/theme/default/style.css?<?php echo time(); ?>">
    <title><?php echo INSTALL_APP_NAME.' V'.INSTALL_APP_VERSION; ?> 安装助手</title>
    <style type="text/css">
        body, html {
            min-height: 100%;
        }
        body {
            background-image:url("data:image/svg+xml;base64,<?php echo base64_encode('<?xml version="1.0" encoding="UTF-8"?><svg width="200" height="200" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><text x="50%" y="50%" font-size="16" fill="#444444" style="transform:rotate(-45deg);transform-origin:center;" font-family="system-ui,sans-serif" text-anchor="middle" dominant-baseline="middle">'.INSTALL_APP_NAME.' V'.INSTALL_APP_VERSION.'</text></svg>'); ?>");
        }
        .pb-installer-box{
            background-image:url("data:image/svg+xml;base64,<?php echo base64_encode('<?xml version="1.0" encoding="UTF-8"?><svg width="100" height="100" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><text x="50%" y="50%" font-size="10" fill="#EEEEEE" style="transform:rotate(-45deg);transform-origin:center;" font-family="system-ui,sans-serif" text-anchor="middle" dominant-baseline="middle">'.INSTALL_APP_NAME.' V'.INSTALL_APP_VERSION.'</text></svg>'); ?>");
            background-color:#FFF;
            border-radius:0.5rem;
        }
        .pb-installer-box .step-menu{
            padding:0 0.5rem;
            text-align: center;
            border-bottom:1px solid #EEE;
        }
        .pb-installer-box .step-menu a{
            display:inline-block;
            padding:0.8rem;
            color:var(--color-text);
            text-align:center;
            border-bottom:1px solid #EEE;
            font-size:0.8rem;
        }
        .pb-installer-box .step-menu > .iconfont{
            color:var(--color-muted);
            font-size:0.8rem;
        }
        .pb-installer-box .step-menu a.checked{
            color:var(--color-primary);
        }
        .pb-installer-box .step-menu a.active{
            color:var(--color-primary);
        }
        .pb-installer-box .step-content .step-content-item{
            display:none;
        }
        .pb-installer-box .step-content .step-content-item.active{
            display:block;
        }
        .pb-installer-box .step-content .step-content-item .content-item-body{
            padding:1rem;
        }
        .pb-installer-box .step-content .step-content-item .content-item-foot{
            padding: 1rem 1rem 2rem 1rem;
            text-align:center;
        }
        .pb-installer-box .step-content .step-content-item iframe{
            width:100%;
            border:1px solid #EEE;
            border-radius:0.25rem;
            height:calc( 100vh - 200px );
        }
    </style>
</head>
<body style="background-color:#333;padding:40px 0;">
<div style="width:960px;min-height:100vh;margin:0 auto;">

    <?php if (file_exists(APP_PATH . '/storage/install.lock')) { ?>
        <div class="ub-alert danger ub-text-center">系统无需重复安装</div>
    <?php } else { ?>
        <h1 class="ub-text-center" style="color:#FFF;">
            <i class="iconfont icon-tools"></i>
            <?php echo INSTALL_APP_NAME.' V'.INSTALL_APP_VERSION; ?> 安装
        </h1>
        <div class="pb-installer-box">
            <div class="step-menu">
                <?php if (defined('LICENSE_URL')) { ?>
                    <a href="javascript:;" class="active" data-step="license">
                        <i class="iconfont icon-file"></i>
                        安装协议
                    </a>
                    <i class="iconfont icon-angle-right"></i>
                <?php } ?>
                <a href="javascript:;" class="<?php if(!defined('LICENSE_URL')) { echo 'active'; } ?>" data-step="env">
                    <i class="iconfont icon-desktop"></i>
                    环境检查
                </a>
                <i class="iconfont icon-angle-right"></i>
                <a href="javascript:;" data-step="form">
                    <i class="iconfont icon-cog"></i>
                    配置安装
                </a>
            </div>
            <div class="step-content">
                <?php if (defined('LICENSE_URL')) { ?>
                    <div class="step-content-item active" data-step="license">
                        <div class="content-item-body">
                            <iframe src="<?php echo LICENSE_URL; ?>"></iframe>
                        </div>
                        <div class="content-item-foot">
                            <div>
                                <div style="color:red;padding-bottom:1rem;">
                                    禁止使用本产品进行违法违规业务，我们将对违规使用者停止授权并永久封停账号
                                </div>
                                <label style="border:none;margin-top:-10px;margin-right:0;padding-right:0;">
                                    <input type="checkbox" name="installLicense" style="vertical-align:middle;margin-right:0.2rem;" value="1"/>
                                    我已阅读协议并同意所有内容
                                </label>
                            </div>
                            <div style="margin-top:0.5rem;">
                                <a href="javascript:;" class="btn btn-primary btn-lg btn-round" onclick="doStepEnv()">
                                    下一步
                                </a>
                            </div>
                            <script>
                                function doStepEnv(){
                                    if(!$('[name="installLicense"]').is(':checked')){
                                        window.api.dialog.tipError('请先同意协议');
                                        return false;
                                    }
                                    switch_step('env');
                                    return false;
                                }
                            </script>
                        </div>
                    </div>
                <?php } ?>
                <div class="step-content-item <?php if(!defined('LICENSE_URL')) { echo 'active'; } ?>" data-step="env">
                    <div class="content-item-body">
                        <div style="margin:0 auto;">
                            <?php text_success('系统：' . PHP_OS); ?>
                            <?php php_version_ok() ? text_success('PHP版本 ' . PHP_VERSION) : text_error('PHP版本要求（'.php_version_requires().'） 当前为 ' . PHP_VERSION); ?>
                            <?php text_success('最大上传：' . FileUtil::formatByte(EnvUtil::env('uploadMaxSize'))); ?>
                            <?php function_exists('curl_init') ? text_success('PHP curl 扩展') : text_error('缺少 PHP curl 扩展'); ?>
                            <?php class_exists('ZipArchive') ? text_success('PHP zip 扩展') : text_error('缺少 PHP zip 扩展'); ?>
                            <?php function_exists('openssl_open') ? text_success('PHP openssl 扩展') : text_error('缺少 PHP openssl 扩展'); ?>
                            <?php function_exists('exif_read_data') ? text_success('PHP exif 扩展') : text_error('缺少 PHP exif 扩展'); ?>
                            <?php function_exists('bcmul') ? text_success('PHP bcmath 扩展') : text_error('缺少 PHP bcmath 扩展'); ?>
                            <?php class_exists('pdo') ? text_success('PHP pdo 扩展') : text_error('缺少 PHP pdo 扩展'); ?>
                            <?php (class_exists('pdo') && in_array('mysql', PDO::getAvailableDrivers())) ? text_success('PHP PDO mysql 驱动') : text_error('缺少 PHP PDO mysql 驱动'); ?>
                            <?php function_exists('mb_internal_encoding') ? text_success('PHP mbstring 扩展') : text_error('缺少 PHP mbstring 扩展'); ?>
                            <?php function_exists('token_get_all') ? text_success('PHP tokenizer 扩展') : text_error('缺少 PHP tokenizer 扩展'); ?>
                            <?php function_exists('finfo_file') ? text_success('PHP fileinfo 扩展') : text_error('缺少 PHP fileinfo 扩展'); ?>
                            <?php function_exists('proc_open') ? text_success('proc_open 函数') : text_error('缺少 proc_open 函数','https://modstart.com/doc/install/qa.html'); ?>
                            <?php function_exists('putenv') ? text_success('putenv 函数') : text_error('缺少 putenv 函数','https://modstart.com/doc/install/qa.html'); ?>
                            <?php function_exists('proc_get_status') ? text_success('proc_get_status 函数') : text_error('缺少 proc_get_status 函数','https://modstart.com/doc/install/qa.html'); ?>
                            <?php if(version_compare(PHP_VERSION,'5.6.0','ge') && version_compare(PHP_VERSION,'5.7.0','lt')){ ?>
                                <?php EnvUtil::iniFileConfig('always_populate_raw_post_data')=='-1' ? text_success('验证 always_populate_raw_post_data=-1') : text_error('请配置 always_populate_raw_post_data=-1','https://modstart.com/doc/install/qa.html'); ?>
                            <?php } ?>
                            <?php is_dir_really_writable(APP_PATH . '/bootstrap/') ? text_success('/bootstrap/目录可写') : text_error('/bootstrap/目录不可写'); ?>
                            <?php is_dir_really_writable(APP_PATH . '/storage/') ? text_success('/storage/目录可写') : text_error('/storage/目录不可写'); ?>
                            <?php is_dir_really_writable(APP_PATH . '/public/') ? text_success('/public/目录可写') : text_error('/public/目录不可写'); ?>
                            <?php is_dir_really_writable(APP_PATH . '/bootstrap/cache/') ? text_success('/bootstrap/cache/目录可写') : text_error('/bootstrap/cache/目录不可写'); ?>
                            <div data-rewrite-check>
                                <div class="status loading"><div class="ub-alert">Rewrite规则检测中...</div></div>
                                <div class="status success" style="display:none;"><?php text_success('Rewrite规则正确'); ?></div>
                                <div class="status error" style="display:none;"><?php text_error('Rewrite规则错误','https://modstart.com/doc/install/qa.html#q-rewrite%E8%A7%84%E5%88%99',false); ?></div>
                                <div class="status error ub-alert warning" style="display:none;">
                                    <div>- 配置Nginx/Apache，保证访问 <a href="/install/ping" target="_blank">/install/ping</a> 出现 ok 字样。</div>
                                </div>
                            </div>
                            <div data-public-install-path-warning style="display:none;">
                                <?php text_warning('安装路径为 public/install.php ，可能设置错了网站根目录，请仔细检查','https://modstart.com/doc/install/start.html#%E5%8F%82%E8%80%83%E9%85%8D%E7%BD%AE'); ?>
                            </div>
                            <script>
                                if(window.location.href.indexOf('public/install.php')>0){
                                    document.querySelector('[data-public-install-path-warning]').style.display = 'block';
                                }
                            </script>
                        </div>
                    </div>
                    <div class="content-item-foot">
                        <?php if (error_counter(0) > 0) { ?>
                            <div class="ub-alert danger ub-text-center">请解决以上 <?php echo error_counter(0); ?> 个问题再进行安装</div>
                        <?php } else if (!env_writable()) { ?>
                            <div class="ub-alert danger ub-text-center">/.env文件不可写，请手动配置安装</div>
                        <?php } else { ?>
                            <div style="display:none;margin-top:0.5rem;" data-step-env-next>
                                <?php if(defined('LICENSE_URL')) { ?>
                                    <a href="javascript:;" class="btn btn-lg btn-round" onclick="switch_step('license')">
                                        上一步
                                    </a>
                                <?php } ?>
                                <a href="javascript:;" class="btn btn-primary btn-lg btn-round" onclick="switch_step('form')">
                                    下一步
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="step-content-item ub-form" data-step="form">
                    <div class="content-item-body">
                        <div class="ub-panel" style="background:#F8F8F8;">
                            <div class="head">
                                <div class="title">
                                    <i class="iconfont icon-credit"></i>
                                    MySQL数据库
                                </div>
                            </div>
                            <div class="body">
                                <div class="line">
                                    <label class="label"><span class="ub-text-danger">*</span> 主机</label>
                                    <input type="text" style="width:100%;" name="db_host"
                                           value="<?php echo htmlspecialchars(get_env_config('db_host')); ?>"/>
                                </div>
                                <div class="line">
                                    <label class="label"><span class="ub-text-danger">*</span> 端口</label>
                                    <input type="text" style="width:100%;" name="db_port"
                                           value="<?php echo htmlspecialchars(get_env_config('db_port')); ?>"/>
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
                            <div class="ub-panel" style="background:#F8F8F8;">
                                <div class="head">
                                    <div class="title">
                                        <i class="iconfont icon-cog"></i>
                                        应用配置信息
                                    </div>
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
                        <div class="ub-panel" style="background:#F8F8F8;">
                            <div class="head">
                                <div class="title">
                                    <i class="iconfont icon-user-o"></i>
                                    后台管理信息
                                </div>
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
                    </div>
                    <div class="content-item-foot">
                        <?php if (defined('DEMO_DATA')) { ?>
                            <div>
                                <label style="border:none;margin-top:-10px;">
                                    <input type="checkbox" style="vertical-align:middle;" name="installDemo" value="1"/>
                                    初始化安装演示数据
                                </label>
                            </div>
                        <?php } ?>
                        <div style="margin-top:0.5rem;">
                            <input type="hidden" name="installLicense" value="1"/>
                            <input type="hidden" name="INSTALL_CONFIG" value="<?php echo htmlspecialchars(json_encode(isset($INSTALL_CONFIG)?$INSTALL_CONFIG:null)); ?>" />
                            <a href="javascript:;" onclick="switch_step('env')" class="btn btn-lg btn-round">
                                上一步
                            </a>
                            <a href="javascript:;" onclick="doSubmit();" class="btn btn-primary btn-lg btn-round">
                                确定安装
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<script>
    function switch_step(name){
        $('.pb-installer-box .step-content .step-content-item').removeClass('active');
        $('.pb-installer-box .step-content .step-content-item[data-step='+name+']').addClass('active');
        $('.pb-installer-box .step-menu a').removeClass('active');
        $('.pb-installer-box .step-menu a[data-step='+name+']').addClass('active');
        return false;
    }
    $(function () {
        var $rewriteCheck = $('[data-rewrite-check]');
        $.ajax({
            url: '/install/ping?'+Math.random(),
            type: 'GET',
            success: function(data){
                if('ok'===data){
                    $rewriteCheck.find('.status').hide().filter('.success').show();
                    $('[data-step-env-next]').show();
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
