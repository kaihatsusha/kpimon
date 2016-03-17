<?php
namespace app\modules\oef\views;

use yii\web\AssetBundle;

class PurchaseAssetSP extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];

    public $js = [
        'js/oef/purchaseInput.js',
    ];

    public $depends = [
        'app\assets\SmartPhoneAsset',
    ];
}