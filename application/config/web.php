<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'KHT8oQgDpnIXXa3j6MCKDNlcs8tN4Bwd21fff',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => false,
            'loginUrl' => ['/'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '/' => 'index/index',
                'help' => 'help/help',
                'contact' => 'help/contact',
                'authenticate' => 'index/authenticate',
                'entry/<id:\w+>' => 'index/entry',
                'logout' => 'index/logout',
                'thread/add' => 'thread/add',
                'thread/delete' => 'thread/delete',
                'thread/view/<id:\w+>' => 'thread/view',
                'thread/edit/<id:\w+>' => 'thread/edit',
                'thread/change-status' => 'thread/change-status',
                'thread-row/list/<thread_id:\w+>' => 'thread-row/list',
                'thread-row/add/<thread_id:\w+>' => 'thread-row/add',
                'thread-row/edit/<thread_row_id:\w+>' => 'thread-row/edit',
                'thread-row/delete/<thread_row_id:\w+>' => 'thread-row/delete',
                'thread-row/view/<thread_row_id:\w+>' => 'thread-row/view',
                'm/<hash:\w+>' => 'moderator/redirect',
                'authenticate/moderator/<hash:\w+>' => 'moderator/authenticate-moderator',
                'moderator/list/<thread_id:\w+>' => 'moderator/list',
                'moderator/add/<thread_id:\w+>' => 'moderator/add',
                'moderator/delete/<thread_id:\w+>' => 'moderator/delete',
                'list/generate/<thread_id:\w+>' => 'list/generate',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV === 'dev') {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1']  //allowing ip's
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1']  //allowing ip's
    ];
}

return $config;
