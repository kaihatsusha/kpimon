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
        $arrAccount = JarAccount::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'account_type'=>MasterValueUtils::MV_JAR_ACCOUNT_TYPE_JAR])->orderBy('status, order_num')->all();
        $tempAccount = JarAccount::findOne(9);
        $sumAccountQuery = (new Query())->select(['SUM(useable_balance) AS useable_balance, SUM(real_balance) AS real_balance, SUM(share_unit) AS share'])->from('jar_account')->where([
                'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_JAR_ACCOUNT_STATUS_ON, 'account_type'=>MasterValueUtils::MV_JAR_ACCOUNT_TYPE_JAR]);
        $sumAccountValue = $sumAccountQuery->createCommand()->queryOne();

        // render GUI
        $renderData = ['arrAccount'=>$arrAccount, 'tempAccount'=>$tempAccount, 'sumAccountValue'=>$sumAccountValue];

        return $this->render('index', $renderData);
    }
}