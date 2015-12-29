<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

class DefaultAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        //'js/xyz.js',
    ];

    public $css = [
        'css/pc/layout_customize.css',
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];
}