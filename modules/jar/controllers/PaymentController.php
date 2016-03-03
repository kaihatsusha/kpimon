<?php
namespace app\modules\jar\controllers;

use yii\db\Query;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\JarAccount;

class PaymentController extends MobiledetectController {
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update'],
                'rules' => [
                    [
                        'allow' => true, 'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex() {

    }

    public function actionView($id) {

    }

    public function actionCreate() {

    }

    public function actionUpdate($id) {

    }
}