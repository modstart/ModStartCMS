<?php
return [

    'name' => 'ModStart',
    'lang' => [
        'track_missing' => false,
    ],

    'subdirUrl' => env('SUBDIR_URL', null),
    'subdir' => env('SUBDIR', '/'),

    'admin' => [
        'prefix' => trim(env('ADMIN_PATH', 'admin'), '/'),
        'directory' => app_path('Admin'),
        'login' => [
            'captcha' => true,
        ],
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
        'cdn' => '/',
        'image_none' => '',
    ],

    'config' => [
        'driver' => \ModStart\Core\Config\Driver\DatabaseMConfig::class,
    ],

];
