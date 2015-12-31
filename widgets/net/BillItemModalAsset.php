<?php
namespace app\widgets\net;

use yii\web\AssetBundle;

class BillItemModalAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];

    public $js = [
        'js/net/billItemModal.js'
    ];

    public $depends = [
        //'app\assets\AppAsset'
    ];
}