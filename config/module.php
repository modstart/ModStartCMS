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
                'position' => '[{"k":"head","v":"头部导航"},{"k":"foot","v":"底部导航"}]',
            ],
        ],
        'Banner' => [
            'enable' => true,
            'config' => [
                'position' => '[{"k":"home","v":"首页"}]',
            ],
        ],
        'Partner' => [
            'enable' => true,
            'config' => [
                'position' => '[{"k":"home","v":"首页"}]',
            ],
        ],
        'Member' => [
            'enable' => true,
            'config' => [
                'groupEnable' => true,
            ],
        ],
        'Cms' => [
            'enable' => true,
        ],
        'SiteCounter' => [
            'enable' => true,
        ],
//        'CmsThemeCorp' => [
//            'enable' => true,
//        ],
    ],
];
