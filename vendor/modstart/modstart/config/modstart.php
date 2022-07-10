<?php
return [

    'name' => 'ModStart',
    'lang' => [
        'track_missing' => false,
    ],

    'trackPerformance' => env('TRACK_PERFORMANCE', false),
    'statisticServer' => env('STATISTIC_SERVER', null),

    'forceSchema' => env('FORCE_SCHEMA', null),
    'subdirUrl' => env('SUBDIR_URL', null),
    'subdir' => env('SUBDIR', '/'),

    'admin' => [
        'prefix' => trim(env('ADMIN_PATH', 'admin'), '/'),
        'directory' => app_path('Admin'),
        'login' => [
            'captcha' => true,
        ],
        'versionCheckDisable' => env('ADMIN_VERSION_CHECK_DISABLE',false),
        'upgradeDisable' => env('ADMIN_UPGRADE_DISABLE',false),
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
