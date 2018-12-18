<?php
return [
    ['class' => 'yii\rest\UrlRule',
        'controller' => [
            'support-versions',
            'default',
            'v1/user',
            'v1/post',
            'v1/test',
            'v1/article',
            'v1/stark',
            'v1/jwt',
            ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/jwt'
        ],
        'extraPatterns' => [
            'POST create' => 'create',
            'GET jwt' => 'jwt',
            'GET list' => 'list',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/article'
        ],
        // 'except'=>['delete','create','article-update','view'],
        'extraPatterns' => [
            'POST create' => 'create',
            'POST article-update' => 'article-update',
            'GET desc' => 'desc',
            'GET list' => 'list',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/news'
        ],
        // 'except'=>['delete','create','article-update','view'],
        'extraPatterns' => [
            'POST create' => 'create',
            'POST article-update' => 'article-update',
            'GET desc' => 'desc',
            'GET list' => 'list',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/file'
        ],
        // 'except'=>['delete','create','article-update','view'],
        'extraPatterns' => [
            'POST create' => 'create',
            'POST upload-img-data' => 'upload-img-data',
            'GET get-upload-token' => 'get-upload-token',
            'GET get-upload-token' => 'get-upload-token',
            'POST upload-file' => 'Upload-file',
            'GET desc' => 'desc',
            'GET list' => 'list',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/tag'
        ],
        'extraPatterns' => [
            'POST create' => 'create',
            'GET desc' => 'desc',
            'GET list' => 'list',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/category'
        ],
        'extraPatterns' => [
            'POST create' => 'create',
            'GET desc' => 'desc',
            'GET list' => 'list',
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => [
            'v1/stark'
        ],
        'extraPatterns' => [
            'POST create' => 'create',
        ]
    ],
    [
        'class'=>'yii\rest\UrlRule',
        'controller'=>'v1/user',
        'except'=>['delete','create','update','view'],
        'pluralize'=>false,
        'extraPatterns' => [
            'POST login' => 'login',
            'POST signup' => 'signup',
        ]

    ],
];
