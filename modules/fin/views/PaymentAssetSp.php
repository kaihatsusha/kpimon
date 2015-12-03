<?php
namespace app\modules\fin\views;

use yii\web\AssetBundle;

class PaymentAssetSp extends AssetBundle {
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	
	public $css = [
		'css/fin/payment_sp.css'
	];
	
	public $js = [
		//'js/GaRealtime.js',
    ];
	
	public $depends = [
		'app\assets\SmartPhoneAsset',
    ];
}
?>