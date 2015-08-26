<?php
/**
 * @link 
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http:www.yiiframework.com/license/
 * @author nhan
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class WidgetAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/gawidgets.css',
    ];
    public $js = [
		'js/gawidgets.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
