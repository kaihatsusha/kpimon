<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GaRealtimeAsset
 *
 * @author ThuongQBD
 */
namespace app\widgets\gaRealTime;

class GaRealtimeAsset extends \yii\web\AssetBundle{
	public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
		'js/GaRealtime.js',
    ];
    public $depends = [
		'app\assets\AppAsset',
    ];
}
