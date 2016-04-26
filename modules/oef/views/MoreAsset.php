<?php
namespace app\modules\oef\views;

use yii\web\AssetBundle;

class MoreAsset extends AssetBundle {
    public static $CONTEXT = null;

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
    ];

    public $js = [
    ];

    public $depends = [
        'app\assets\AppAsset'
    ];

    public function init() {
        if (isset(self::$CONTEXT['css'])) {
            foreach (self::$CONTEXT['css'] as $more) {
                $this->css[] = $more;
            }
        }

        if (isset(self::$CONTEXT['js'])) {
            foreach (self::$CONTEXT['js'] as $more) {
                $this->js[] = $more;
            }
        }

        if (isset(self::$CONTEXT['depends'])) {
            foreach (self::$CONTEXT['depends'] as $more) {
                $this->depends[] = $more;
            }
        }

        self::$CONTEXT = null;
        parent::init();
    }
}