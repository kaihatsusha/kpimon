<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class FcsaNumberAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/fcsaNumber.js',
        //'js/angular-fcsa-number.js'
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}