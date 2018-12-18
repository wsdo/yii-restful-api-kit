<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu',//yii2-admin的导航菜单
            
        ],
        // 'rbac' => 'dektrium\rbac\RbacWebModule',
        'rbac' => [
            'class' => 'dektrium\rbac\Module',
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableRegistration' => false,
            'enableConfirmation' => false,
            'enableUnconfirmedLogin' => true,
            'enablePasswordRecovery' => true,
            'confirmWithin' => 21600,
            'rememberFor' => 1209600, //如果没有点击记住密码则默认保持1天的登录时间
            'admins' => ['admin'],
            'modelMap' => [
                'User' => 'app\models\User',
                // 'Profile' => 'app\models\Profile',
            ],
        ],
    ],
    'components' => [
        'authManager' => [
             'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\DbManager'
             // 'defaultRoles' => ['未登录用户'],//添加此行代码，指定默认规则为 '未登录用户'
             'defaultRoles' => ['guest'],
             // 'db'=>'dbAdmin',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '4xL65Kc_SNk2PZ66T2zBA5ea_VAVeoRQ',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // 'user' => [
        //     'identityClass' => 'app\models\User',
        //     'enableAutoLogin' => true,
        // ],
        'wechat' => [
            'class' => 'callmez\wechat\sdk\Wechat',
            'appId' => self::env('WEIXIN_APP_ID'),
            'appSecret' => self::env('WEIXIN_APP_SECRET'),
            'token' => self::env('WEIXIN_TOKEN')
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'en',
                    'fileMap' => [
                        //'main' => 'main.php',
                    ],
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'loginUrl' => ['site/login'],
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'qiniu' => [
            'class' => 'app\components\QiniuComponent',
            'accessKey' => '3ZjDv6oRksmoiHk-46GqNSZKEJCcW7YKV4bC-B-L',
            'secretKey' => 'kxoSH8n4EAA3IhlBrM6LOOBZJqidb2mzNgGP7lZc',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            //'enableStrictParsing' => true,
            // 'showScriptName' => false,
            'rules' => require('urls.php'),
        ],
        // 'as access' => [
        //     'class' => 'mdm\admin\components\AccessControl',
        //     'allowActions' => [
        //         'site/login',
        //         // 'admin/*',
        //         // 'some-controller/some-action',
        //         // The actions listed here will be allowed to everyone including guests.
        //         // So, 'admin/*' should not appear here in the production, of course.
        //         // But in the earlier stages of your development, you may probably want to
        //         // add a lot of actions here until you finally completed setting up rbac,
        //         // otherwise you may not even take a first step.
        //     ]
        // ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
