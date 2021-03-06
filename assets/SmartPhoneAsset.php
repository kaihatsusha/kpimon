<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class SmartPhoneAsset extends AssetBundle {
	public $basePath = '@webroot';
	public $baseUrl = '@web';
	
	public $js = [
		//'js/xyz.js',
	];
	
	public $css = [
		'css/layout_customize.css',
		'css/sp/layout_customize.css'
	];
	
	public $jsOptions = [
		'position' => View::POS_HEAD,
	];
	
	public $depends = [
		'app\assets\AppAsset',
    ];
}