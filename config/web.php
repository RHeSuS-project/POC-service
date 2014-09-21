<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'VkyJK6gaVPR8B07cJieRt_JFB6SjSeLV',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'device'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'service'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'charasteristic'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'subscription'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'patient'],                
            ],

        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
	    'defaultRoles' => ['guest'],
        ],
    ],
    'modules'=>array(
        'gii'=>array(
            'class'=>'yii\gii\Module',
            'allowedIPs'=>['192.168.1.142','192.168.1.144','127.0.0.1'],
        ),
        'rbac' => [
            'class' => 'app\modules\rbac\Module',
        ],

    ),
    
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

 //   $config['bootstrap'][] = 'gii';
 //   $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
