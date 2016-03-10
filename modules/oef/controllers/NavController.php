<?php
namespace app\modules\oef\controllers;

//use yii\db\Query;
//use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
//use app\models\JarAccount;

class NavController extends MobiledetectController {
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true, 'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex() {
        // render GUI
        $renderData = [];

        return $this->render('index', $renderData);
    }
}