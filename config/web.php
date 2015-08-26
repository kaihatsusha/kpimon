<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'kpimon',
	'name' => 'KpiMon',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => require(__DIR__ . '/components.php'),
	'modules'=>  require(__DIR__ . '/modules.php'),
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
