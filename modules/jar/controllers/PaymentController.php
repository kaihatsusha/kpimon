<?php
namespace app\modules\jar\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\ModelUtils;
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
        $sumCurrentMonthQuery->from('jar_payment')->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
        $sumCurrentMonthQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_JAR_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_JAR_ACCOUNT_NONE]]);
        $sumCurrentMonthQuery->andWhere(['>=', 'entry_date', $beginMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentMonthQuery->andWhere(['<=', 'entry_date', $endMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentMonthData = $sumCurrentMonthQuery->createCommand()->queryOne();

        // sum Debit Amount & Credit Amount
        $sumEntryValue = false;
        // query for dataprovider
        $dataQuery = null;
        if ($searchModel->validate()) {
            $dataQuery = JarPayment::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            $sumEntryQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
            $sumEntryQuery->from('jar_payment')->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            $sumEntryQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_JAR_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_JAR_ACCOUNT_NONE]]);

            if (!empty($searchModel->entry_date_from)) {
                $dataQuery->andWhere(['>=', 'entry_date', $searchModel->entry_date_from]);
                $sumEntryQuery->andWhere(['>=', 'entry_date', $searchModel->entry_date_from]);
            }
            if (!empty($searchModel->entry_date_to)) {
                $dataQuery->andWhere(['<=', 'entry_date', $searchModel->entry_date_to]);
                $sumEntryQuery->andWhere(['<=', 'entry_date', $searchModel->entry_date_to]);
            }
            if ($searchModel->account_source > 0) {
                $dataQuery->andWhere(['=', 'account_source', $searchModel->account_source]);
            }
            if ($searchModel->account_target > 0) {
                $dataQuery->andWhere(['=', 'account_target', $searchModel->account_target]);
            }
            $dataQuery->orderBy('entry_date DESC, id DESC');
            $sumEntryValue = $sumEntryQuery->createCommand()->queryOne();
        } else {
            $dataQuery = JarPayment::find()->where(['id'=>-1]);
        }

        // render GUI
        $renderData = ['searchModel'=>$searchModel, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui,
            'arrAccount'=>$arrAccount, 'dataQuery'=>$dataQuery, 'sumEntryValue'=>$sumEntryValue, 'sumCurrentMonthData'=>$sumCurrentMonthData];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {
        $this->objectId = $id;
        $model = JarPayment::findOne(['id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

        $renderView = 'view';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('jar.models', 'Payment')]));
        } else {
            // master value
            $arrAccount = ModelUtils::getArrData(JarAccount::find()->select(['account_id', 'account_name']), 'account_id', 'account_name');
            // data for rendering
            $renderData = ['model'=>$model, 'arrAccount'=>$arrAccount];
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    public function actionCreate() {
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        JarPayment::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $arrAccount = ModelUtils::getArrData(JarAccount::find()->select(['account_id', 'account_name'])
            ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_JAR_ACCOUNT_STATUS_ON])
            ->orderBy('account_type, order_num'), 'account_id', 'account_name');

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = new JarPayment();
        $model->load($postData);
        if (Yii::$app->request->getIsGet()) {
            $model->entry_date = DateTimeUtils::formatNow($fmShortDatePhp);
        }

        // init value
        $model->scenario = MasterValueUtils::SCENARIO_CREATE;

        // render GUI
        $renderView = 'create';
        $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'arrAccount'=>$arrAccount];
        switch ($submitMode) {
            case MasterValueUtils::SM_MODE_INPUT:
                $isValid = $model->validate();
                if ($isValid) {
                    $renderView = 'confirm';
                    $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                }
                break;
            case MasterValueUtils::SM_MODE_CONFIRM:
                $isValid = $model->validate();
                if ($isValid) {
                    $result = $this->createPayment($model, $fmShortDatePhp);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('jar.models', 'Payment')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        // restore Data for View
                        $model->entry_date = DateTimeUtils::parse($model->entry_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                        // render View
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                    }
                }
                break;
            case MasterValueUtils::SM_MODE_BACK:
                break;
            default:
                break;
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    /**
     * create Payment
     * @param $payment JarPayment
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function createPayment($payment, $fmShortDatePhp) {
        // modify data for DB
        $payment->entry_date = DateTimeUtils::parse($payment->entry_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            $accountSource = JarAccount::findOne($payment->account_source);
            $accountTarget = JarAccount::findOne($payment->account_target);
            if ($payment->account_source == 0 || $payment->account_target == 0) {
                // save source
                if (!is_null($accountSource) && ($save !== false)) {
                    $accountSource->real_balance = $accountSource->real_balance - $payment->entry_value;
                    $accountSource->useable_balance = $accountSource->useable_balance - $payment->entry_value;
                    $save = $accountSource->save();
                }
                // save target
                if (!is_null($accountTarget) && ($save !== false)) {
                    $accountTarget->real_balance = $accountTarget->real_balance + $payment->entry_value;
                    $accountTarget->useable_balance = $accountTarget->useable_balance + $payment->entry_value;
                    $save = $accountTarget->save();
                }
            } else {
                // save source
                if ($save !== false) {
                    $accountSource->useable_balance = $accountSource->useable_balance - $payment->entry_value;
                    $save = $accountSource->save();
                }
                // save target
                if ($save !== false) {
                    $accountTarget->useable_balance = $accountTarget->useable_balance + $payment->entry_value;
                    $save = $accountTarget->save();
                }
            }
            // save payment
            if ($save !== false) {
                $payment->share_id = MasterValueUtils::MV_JAR_ACCOUNT_NONE;
                $payment->entry_status = MasterValueUtils::MV_JAR_ENTRY_TYPE_SIMPLE;
                $save = $payment->save();
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('jar.models', 'Payment')]);
        }

        // end transaction
        try {
            if ($save === false) {
                $transaction->rollback();
                return $message;
            } else {
                $transaction->commit();
            }
        } catch(Exception $e) {
            throw Exception(Yii::t('common', 'Unable to excute Transaction.'));
        }

        return true;
    }

    public function actionUpdate($id) {

    }
}