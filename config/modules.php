<?php
return [
	'api' => [
		'class' => 'app\modules\api\Module',
		'modules' => [
			'v1' => 'app\modules\api\v1\Module',
			'ga' => 'app\modules\api\ga\Module'
		],
	],
	'fin' => [
		'class' => 'app\modules\fin\Module'
	],
	'net' => [
		'class' => 'app\modules\net\Module',
	],
	'oth' => [
		'class' => 'app\modules\oth\Module',
	],
	'jar' => [
		'class' => 'app\modules\jar\Module',
	]
];
