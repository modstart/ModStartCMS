<?php
return [
    'baseUrl' => env('DATA_BASE_URL', '/'),
    'upload' => [
        'image' => [
            'maxSize' => 1024 * 1024 * 1024,
            'maxWidth' => 9999,
            'maxHeight' => 9999,
            'extensions' => ['jpg', 'png', 'gif', 'jpeg', 'ico', 'webp'],
            'compress' => true,
        ],
        'video' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => ['mp4', 'flv']
        ],
        'audio' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => ['mp3']
        ],
        'file' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => [
                'svg', 'png', 'jpg', 'jpeg', 'ico', 'webp',
                'mp4',
                'pdf', 'txt', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'csv',
                'zip',
                'epub',
                'ai', 'psd', 'cad',
                'css', 'html',
            ]
        ],
        'document' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => ['jpg', 'png', 'pdf', 'bmp', 'mov']
        ],
        'zip' => [
            'maxSize' => 1024 * 1024 * 1024,
            'extensions' => ['zip']
        ]
    ]
];
