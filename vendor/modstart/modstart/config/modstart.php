<?php
return [

    'name' => 'ModStart',

    'trackMissingLang' => false,
    // 开启性能追踪，开启后会在日志中记录必要的请求，如慢SQL执行、多查询请求
    'trackPerformance' => env('TRACK_PERFORMANCE', false),
    // 慢SQL阈值，单位毫秒
    'trackLongSqlThreshold' => env('TRACK_LONG_SQL_THRESHOLD', 5000),
    'statisticServer' => env('STATISTIC_SERVER', null),

    'forceSchema' => env('FORCE_SCHEMA', null),
    'subdirUrl' => env('SUBDIR_URL', null),
    'subdir' => env('SUBDIR', '/'),

    // 防止X-Forwarded-Host直接访问，遇到通过CDN域名直接访问的情况，直接跳转到主域名
    'xForwardedHostVisitRedirect' => true,

    'admin' => [
        'prefix' => trim(env('ADMIN_PATH', 'admin'), '/'),
        'directory' => app_path('Admin'),
        'login' => [
            'captcha' => true,
        ],
        'versionCheckDisable' => env('ADMIN_VERSION_CHECK_DISABLE', false),
        'upgradeDisable' => env('ADMIN_UPGRADE_DISABLE', false),
        'theme' => env('ADMIN_THEME', 'default'),
        'tabsEnable' => env('ADMIN_TABS_ENABLE', true),
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

];
