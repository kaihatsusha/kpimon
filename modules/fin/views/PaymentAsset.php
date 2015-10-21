<?php
namespace app\modules\fin\views;

class PaymentAsset extends \yii\web\AssetBundle {
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