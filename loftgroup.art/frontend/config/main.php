<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
	'defaultRoute' => 'site/index',//
	'language' => 'ru',//
	'charset' => 'UTF-8',//
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
		'components' =>
		[
				'request' =>
				[
					'enableCsrfValidation' => false,
					'baseUrl' => '',
					'parsers' => ['application/json' => 'yii\web\JsonParser', ]
				],
				'assetManager' =>
				[
					'bundles' =>
					[
						'yii\web\JqueryAsset' =>
						[
							'js' => [YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'],
							'jsOptions' => ['position' => \yii\web\View::POS_HEAD ],
						],
						'yii\bootstrap\BootstrapAsset' => ['css' => [YII_ENV_DEV ? 'css/bootstrap.css' : 'css/bootstrap.min.css']],
						'yii\bootstrap\BootstrapPluginAsset' => ['js' => [YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js']]
					],
				],
				'user' =>
				[
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' =>
				[
					'enablePrettyUrl' => true,
					'showScriptName' => false,
					'enableStrictParsing' => true,
					'rules' =>
					[
						'' => 'site/index',
						'projects' => 'projects/index',
						'projects/more' => 'projects/more',
						'projects/<category:[\d\w_-]+>' => 'projects/category',
						'projects/<category:[\d\w_-]+>/<url:[\d\w_-]+>' => 'projects/item',
						[
							'pattern' => '<action>',
							'route' => 'site/<action>',
							'suffix' => ''
						],
						[
							'pattern' => '<controller>/<action>',
							'route' => '<controller>/<action>',
							'suffix' => ''
						],
					]
				],
    ],
    'params' => $params,
];
