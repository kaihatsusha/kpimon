<?php
namespace app\modules\jar\controllers;

use yii\db\Query;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\JarAccount;

class AccountController extends MobiledetectController {
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
        $arrAccount = JarAccount::find()->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])->orderBy('status, order_num')->all();
        $sumAccountQuery = (new Query())->select(['SUM(credit) AS credit, SUM(debit) AS debit, SUM(share_unit) AS share'])->from('jar_account')->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_JAR_ACCOUNT_STATUS_ON]);
        $sumAccountValue = $sumAccountQuery->createCommand()->queryOne();

        // render GUI
        $renderData = ['arrAccount'=>$arrAccount, 'sumAccountValue'=>$sumAccountValue];

        return $this->render('index', $renderData);
    }
}