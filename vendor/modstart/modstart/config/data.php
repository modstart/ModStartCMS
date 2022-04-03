<?php
return [
    'baseUrl' => env('DATA_BASE_URL', '/'),
    'upload' => [
        'image' => [
            'maxSize' => 1024 * 1024 * 1024,
            'maxWidth' => 9999,
            'maxHeight' => 9999,
            'extensions' => ['jpg', 'png', 'gif', 'jpeg', 'ico'],
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
                'pdf', 'txt', 'svg', 'png', 'jpg',
                'ppt', 'pptx',
                'doc', 'docx',
                'xls', 'xlsx',
                'zip', 'csv',
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
