<?php
return [
    'baseUrl' => env('DATA_BASE_URL', '/'),
    'upload' => [
        'image' => [
            'maxSize' => 1024 * 1024 * 1024,
            'maxWidth' => 9999,
            'maxHeight' => 9999,
            'extensions' => [
                'jpg', 'png', 'gif', 'jpeg', 'ico', 'webp', 'svg',
            ],
            // 是否上传前端压缩
            'compress' => true,
            // 前端压缩图片时保持的最大宽度或高度
            'compressMaxWidthOrHeight' => 4000,
            // 前端压缩图片时尽量压缩到该大小以下
            'compressMaxSize' => 10 * 1024 * 1024,
        ],
        'video' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => [
                'mp4', 'mov',
            ]
        ],
        'audio' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => [
                'mp3', 'wav',
            ]
        ],
        'file' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => [
                'svg', 'png', 'jpg', 'jpeg', 'ico', 'webp',
                'mp4', 'mp3',
                'pdf', 'txt', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'csv',
                'zip',
                'epub',
                'ai', 'psd', 'dwg',
                'css', 'html',
                'ttf',
                'log',
            ]
        ],
        'document' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => [
                'jpg', 'png', 'pdf', 'bmp', 'mov'
            ]
        ],
        'zip' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => [
                'zip'
            ]
        ]
    ]
];
