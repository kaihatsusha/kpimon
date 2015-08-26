<?php

namespace app\modules\api\ga;

use Yii;

class Module extends \app\modules\api\Module
{
    public $controllerNamespace = '\app\modules\api\ga\controllers';

    public function init()
    {
        parent::init();
        Yii::$app->user->identityClass = '\app\modules\api\v1\models\ApiUserIdentity';
        Yii::$app->user->enableSession = false;
        Yii::$app->user->loginUrl = null;
    }
}
