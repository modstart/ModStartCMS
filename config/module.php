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
                'position' => '[{"k":"","v":"独立页面"},{"k":"foot","v":"底部文章"}]',
            ],
        ],
        'Nav' => [
            'enable' => true,
            'config' => [
                'position' => '[{"k":"head","v":"头部导航"}]',
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
        ],
        'Cms' => [
            'enable' => true,
        ],
//        'Ad' => [
//            'enable' => true,
//            'config' => [
//                'position' => '[{"k":"home","v":"首页右侧"}]',
//            ]
//        ],
    ],
];
