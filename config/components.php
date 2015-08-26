<?php
return [
	'request' => [
		// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
		'cookieValidationKey' => 'sBtrAA4qaxOdvf__4IRPWXd3FOC3eGYf',
	],
	'cache' => [
		'class' => 'yii\caching\FileCache',
	],
	'memCache' => [
		'class' => 'app\components\MemCache',
		'servers' => [
			[
				'host' => '192.168.1.20',
				'port' => 11211,
			],
		],
	],
	'user' => [
		'identityClass' => 'app\models\User',
		'loginUrl'=>['site/login'],
		'enableAutoLogin' => true,
	],
	'errorHandler' => [
		'errorAction' => 'site/error',
	],
	'urlManager' => require(__DIR__ . '/_urlManager.php'),
	'assetManager' => [
		'appendTimestamp' => true,
//		'basePath' => '@webroot/asset',
//		'baseUrl' => '@web/asset'
		// 'bundles' => false,
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
	'i18n' => [
        'translations' => [
            '*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                'sourceLanguage' => 'en',
                'fileMap' => [
                    //'common' => 'common.php',
                ],
            ],
        ],
    ],
	'db' => require(__DIR__ . '/db.php'),
];
