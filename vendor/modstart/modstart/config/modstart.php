<?php
return [

    'name' => 'ModStart',

    'trackMissingLang' => false,
    /**
     * 开启性能追踪，开启后会在日志中记录必要的请求，如慢SQL执行、多查询请求
     */
    'trackPerformance' => env('TRACK_PERFORMANCE', false),
    /**
     * 慢SQL阈值，单位毫秒
     */
    'trackLongSqlThreshold' => env('TRACK_LONG_SQL_THRESHOLD', 5000),
    'statisticServer' => env('STATISTIC_SERVER', null),

    'forceSchema' => env('FORCE_SCHEMA', null),
    'subdirUrl' => env('SUBDIR_URL', null),
    'subdir' => env('SUBDIR', '/'),

    // 防止X-Forwarded-Host直接访问，遇到通过CDN域名直接访问的情况，直接跳转到主域名
    'xForwardedHostVisitRedirect' => true,

    'admin' => [

        /**
         * 后台自定义标题
         */
        'title' => null,

        'disabled' => env('ADMIN_DISABLED', false),
        /**
         * 后台管理路径
         */
        'prefix' => trim(env('ADMIN_PATH', 'admin'), '/'),
        'directory' => app_path('Admin'),
        'login' => [
            /**
             * 后台登录页面验证码
             */
            'captcha' => true,
        ],
        'versionCheckDisable' => env('ADMIN_VERSION_CHECK_DISABLE', false),
        'upgradeDisable' => env('ADMIN_UPGRADE_DISABLE', false),
        'theme' => env('ADMIN_THEME', 'light'),
        'tabsEnable' => env('ADMIN_TABS_ENABLE', true),
        'styles' => [],
        /**
         * 后台请求忽略权限校验的 Controller 或 Action
         * 例如：['\App\Admin\Controller\AuthController@login', '\App\Admin\Controller\AuthController']
         */
        'authIgnores' => [],
        /**
         * 后台多语言配置
         */
        'i18n' => [
            /**
             * 后台是否开启多语言
             */
            'enable' => false,
            'langs' => [
                'zh' => '简体中文',
                'en' => 'English',
            ]
        ]
    ],

    'web' => [
        'prefix' => trim(env('APP_PATH', ''), '/'),
        'directory' => app_path('Web'),
    ],

    'api' => [
        'prefix' => trim(env('API_PATH', 'api'), '/'),
        'directory' => app_path('Api'),
    ],

    'openApi' => [
        'prefix' => trim(env('API_PATH', 'open_api'), '/'),
        'directory' => app_path('OpenApi'),
    ],

    'asset' => [
        'driver' => \ModStart\Core\Assets\Driver\LocalAssetsPath::class,
        'cdn' => env('CDN_URL', '/'),
        'image_none' => '',
    ],

    'config' => [
        'driver' => \ModStart\Core\Config\Driver\DatabaseMConfig::class,
    ],

    'i18n' => [
        'langs' => [
            'zh' => '简体中文',
            'en' => 'English',
        ]
    ]

];
