<?php
namespace app\modules\fin\views;

use yii\web\AssetBundle;

class PaymentAsset extends AssetBundle {
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	
	public $css = [
		'css/fin/payment.css'
	];
	
	public $js = [
		//'js/GaRealtime.js',
    ];
	
	public $depends = [
		'app\assets\AppAsset',
    ];
}
?>