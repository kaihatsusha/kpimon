<?php

return [
    'class'=>'yii\web\UrlManager',
	'enablePrettyUrl'=>true,
	'showScriptName'=>false,
	'rules'=> [
		// Api
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/user', 'only' => ['index', 'view', 'options']]
	]
];
