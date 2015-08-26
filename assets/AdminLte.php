<?php
namespace app\assets;

use yii\web\AssetBundle;

class AdminLte extends AssetBundle
{
    public $sourcePath = '@bower/admin-lte';
    public $js = [
        'dist/js/app.js', 'plugins/sparkline/jquery.sparkline.min.js',
		//E:\working\devsource\kpi_mon\vendor\bower\admin-lte\plugins\sparkline\jquery.sparkline.min.js
    ];
    public $css = [
        'dist/css/AdminLTE.min.css',
		'dist/css/skins/_all-skins.min.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\FontAwesome',
        'app\assets\JquerySlimScroll'
    ];
}
