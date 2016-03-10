<?php
namespace app\modules\jar\controllers;

use yii\db\Query;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\JarAccount;
use app\models\JarPayment;

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
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        JarPayment::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $arrAccount = ModelUtils::getArrData(JarAccount::find()->select(['account_id', 'account_name'])
            ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
            ->orderBy('account_type, status, order_num'), 'account_id', 'account_name');
        $searchModel = new JarPayment();

        // submit data
        $postData = Yii::$app->request->post();

        // populate model attributes with user inputs
        $searchModel->load($postData);

        // init value
        $today = DateTimeUtils::getNow();
        if (Yii::$app->request->getIsGet()) {
            $tdInfo = getdate($today->getTimestamp());
            $searchModel->entry_date_to = $today->format($fmShortDatePhp);
            $searchModel->entry_date_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        }
        $searchModel->scenario = MasterValueUtils::SCENARIO_LIST;

        // sum current month
        $beginMonth = DateTimeUtils::parse($today->format(DateTimeUtils::FM_DEV_YM) . '01', DateTimeUtils::FM_DEV_DATE);
        $endMonth = DateTimeUtils::addDateTime($beginMonth, 'P1M');
        DateTimeUtils::subDateTime($endMonth, 'P1D', null, false);
        $sumCurrentMonthQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
        $sumCurrentMonthQuery->from('jar_payment')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
        $sumCurrentMonthQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_JAR_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_JAR_ACCOUNT_NONE]]);
        $sumCurrentMonthQuery->andWhere(['>=', 'entry_date', $beginMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentMonthQuery->andWhere(['<=', 'entry_date', $endMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentMonthData = $sumCurrentMonthQuery->createCommand()->queryOne();
        var_dump($sumCurrentMonthData);die();
    }

    public function actionView($id) {

    }

    public function actionCreate() {

    }

    public function actionUpdate($id) {

    }
}