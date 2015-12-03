<?php
namespace app\assets;

use yii\web\AssetBundle;

class ChartSparklineAsset extends AssetBundle {
    public $sourcePath = '@bower/admin-lte';

    public $js = [
        'plugins/sparkline/jquery.sparkline.min.js'
    ];

    public $css = [
    ];

    public $depends = [
    ];
}