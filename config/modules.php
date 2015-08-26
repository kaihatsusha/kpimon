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
		'class' => 'app\modules\fin\Module',
		'modules' => [
			'ga' => 'app\modules\fin\ga\Module'
		],
	]
];
