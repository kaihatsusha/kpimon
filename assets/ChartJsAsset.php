<?php
namespace app\assets;

use yii\web\AssetBundle;

class ChartJsAsset extends AssetBundle {
    public $sourcePath = '@bower/admin-lte';

    public $js = [
        'plugins/chartjs/Chart.min.js'
    ];

    public $css = [
    ];

    public $depends = [
    ];
}