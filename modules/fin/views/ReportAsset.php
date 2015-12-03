<?php
namespace app\modules\fin\views;

use yii\web\AssetBundle;

class ReportAsset extends AssetBundle {
    public static $CONTEXT = null;

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];

    public $js = [
        'js/fin/report.js'
    ];

    public $depends = [
        'app\assets\AppAsset'
    ];

    public function init() {
        $moreJs = self::$CONTEXT['js'];
        if (is_array($moreJs)) {
            foreach ($moreJs as $more) {
                $this->js[] = $more;
            }
        }

        $moreDepends = self::$CONTEXT['depends'];
        if (is_array($moreDepends)) {
            foreach ($moreDepends as $more) {
                $this->depends[] = $more;
            }
        }

        self::$CONTEXT = null;
        parent::init();
    }
}