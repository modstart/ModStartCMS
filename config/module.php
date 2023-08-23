<?php
return [
    'system' => [
        'Vendor' => [
            'enable' => true,
        ],
        'AdminManager' => [
            'enable' => true,
        ],
        'ModuleStore' => [
            'enable' => true,
        ],
        'Site' => [
            'enable' => true,
        ],
        'Article' => [
            'enable' => true,
            'config' => [
                'position' => '[{"k":"","v":"单页"},{"k":"simple","v":"简单页面"}]',
            ],
        ],
        'Nav' => [
            'enable' => true,
            'config' => [
                'position' => '[{"k":"head","v":"头部导航"},{"k":"foot","v":"底部导航"},{"k":"footSecondary","v":"底部次导航"}]',
            ],
        ],
        'Banner' => [
            'enable' => true
        ],
        'Partner' => [
            'enable' => true,
        ],
        'CaptchaTecmz' => [
            'enable' => true,
        ],
        'SmsTecmz' => [
            'enable' => true,
        ],
        'Member' => [
            'enable' => true,
            'config' => [
                'groupEnable' => true,
                'vipEnable' => true,
            ],
        ],
        'VisitStatistic' => [
            'enable' => true,
        ],
        'Cms' => [
            'enable' => true,
        ],
    ],
];
