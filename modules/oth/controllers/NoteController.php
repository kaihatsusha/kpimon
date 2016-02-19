<?php
namespace app\modules\oth\controllers;

use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\OthNote;

class NoteController extends MobiledetectController {
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
        $dataQuery = OthNote::find()->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
            ->orderBy('order_num');
        $arrModel = $dataQuery->all();

        // render GUI
        $renderData = ['arrModel'=>$arrModel];

        return $this->render('index', $renderData);
    }
}