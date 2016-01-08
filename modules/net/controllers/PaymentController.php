<?php
namespace app\modules\net\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\EnDescrypt;
use app\components\MasterValueUtils;
use app\components\ModelUtils;
use app\controllers\MobiledetectController;
use app\models\NetCustomer;
use app\models\NetPayment;

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
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        $arrNetCustomer = ModelUtils::getArrData(NetCustomer::find()->select(['id', 'name'])
            ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_NET_CUSTOMER_STATUS_ON])
            ->orderBy('order_num'), 'id', 'name');
        NetPayment::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $searchModel = new NetPayment();

        // submit data
        $postData = Yii::$app->request->post();

        // populate model attributes with user inputs
        $searchModel->load($postData);

        // init value
        if (Yii::$app->request->getIsGet()) {
            $today = DateTimeUtils::getNow();
            $tdInfo = getdate($today->getTimestamp());
            $searchModel->entry_date_to = $today->format($fmShortDatePhp);
            $searchModel->entry_date_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        }
        $searchModel->scenario = MasterValueUtils::SCENARIO_LIST;

        // sum Debit Amount & Credit Amount
        $sumEntryValue = false;
        // query for dataprovider
        $dataQuery = null;
        if ($searchModel->validate()) {
            $dataQuery = NetPayment::find()->select('t1.*, t2.bill_date')->from('net_payment t1')
                ->leftJoin('net_bill t2', 't1.order_id = t2.id')->where(['=', 't1.delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            $sumEntryQuery = (new Query())->select(['SUM(credit) AS credit, SUM(debit) AS debit'])->from('net_payment')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            if (!empty($searchModel->entry_date_from)) {
                $searchDate = DateTimeUtils::parse($searchModel->entry_date_from, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
                $dataQuery->andWhere(['>=', 't1.entry_date', $searchDate]);
                $sumEntryQuery->andWhere(['>=', 'entry_date', $searchDate]);
            }
            if (!empty($searchModel->bill_date_to)) {
                $searchDate = DateTimeUtils::parse($searchModel->entry_date_to, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
                $dataQuery->andWhere(['<=', 't1.entry_date', $searchDate]);
                $sumEntryQuery->andWhere(['<=', 'entry_date', $searchDate]);
            }
            if ($searchModel->customer_id > 0) {
                $dataQuery->andWhere(['=', 't1.customer_id', $searchModel->customer_id]);
                $sumEntryQuery->andWhere(['=', 'customer_id', $searchModel->customer_id]);
            }
            $dataQuery->orderBy('entry_date DESC, update_date DESC');
            $sumEntryValue = $sumEntryQuery->createCommand()->queryOne();
        } else {
            $dataQuery = NetPayment::find()->where(['customer_id'=>-1]);
        }

        // render GUI
        $renderData = ['searchModel'=>$searchModel, 'fmShortDateJui'=>$fmShortDateJui, 'arrNetCustomer'=>$arrNetCustomer, 'dataQuery'=>$dataQuery, 'sumEntryValue'=>$sumEntryValue];

        return $this->render('index', $renderData);
    }

    private function getPaymentModel($id) {
        $result = NetPayment::findOne(['secret_key'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
        if (is_null($result)) {
            return null;
        }

        $secretKey = EnDescrypt::encryptSha1($result->customer_id . $result->entry_date);
        return ($id == $secretKey) ? $result : null;
    }

    public function actionView($id) {
        $this->objectId = $id;
        $model = $this->getPaymentModel($id);

        $renderView = 'view';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Payment')]));
        } else {
            // master value
            $arrNetCustomer = ModelUtils::getArrData(NetCustomer::find()->select(['id', 'name'])
                ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                ->orderBy('order_num'), 'id', 'name');

            // data for rendering
            $renderData = ['model'=>$model, 'arrNetCustomer'=>$arrNetCustomer];
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    public function actionCreate() {
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        $arrNetCustomer = ModelUtils::getArrData(NetCustomer::find()->select(['id', 'name'])
            ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_NET_CUSTOMER_STATUS_ON])
            ->orderBy('order_num'), 'id', 'name');
        NetPayment::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $model = new NetPayment();

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model->load($postData);
        if (Yii::$app->request->getIsGet()) {
            $model->entry_date = DateTimeUtils::formatNow($fmShortDatePhp);
        }

        // init value
        $model->scenario = MasterValueUtils::SCENARIO_CREATE;

        // render GUI
        $renderView = 'create';
        $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'arrNetCustomer'=>$arrNetCustomer];
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
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Payment')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        // modify data for View
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
        return $this->render($renderView, $renderData);
    }

    /**
     * create a Payment
     * @param $paymentModel
     * @param $fmDateTimePhp
     * @throws Exception
     * @return string|true
     */
    private function createPayment($paymentModel, $fmDateTimePhp) {
        // modify data for DB
        $paymentModel->entry_date = DateTimeUtils::parse($paymentModel->entry_date, $fmDateTimePhp, DateTimeUtils::FM_DB_DATE);

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save Payment
            $paymentModel->debit = 0;
            $paymentModel->order_id = 0;
            $paymentModel->secret_key = EnDescrypt::encryptSha1($paymentModel->customer_id . $paymentModel->entry_date);
            $save = $paymentModel->save(false);

            // save Customer
            $customer = NetCustomer::findOne(['id'=>$paymentModel->customer_id]);
            if ($save !== false && !is_null($customer)) {
                $customer->balance += $paymentModel->credit;
                $save = $customer->save();
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Payment')]);
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
        $this->objectId = $id;
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $model = $this->getPaymentModel($id);
        NetPayment::$_PHP_FM_SHORTDATE = $fmShortDatePhp;

        $renderView = 'update';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Payment')]));
        } else {
            // back up data
            $model->credit_old = $model->credit;
            $model->entry_date_old = $model->entry_date;

            // master value
            $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
            $arrNetCustomer = ModelUtils::getArrData(NetCustomer::find()->select(['id', 'name'])
                ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_NET_CUSTOMER_STATUS_ON])
                ->orderBy('order_num'), 'id', 'name');

            // submit data
            $postData = Yii::$app->request->post();
            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

            // populate model attributes with user inputs
            $model->load($postData);

            // init value
            $model->scenario = MasterValueUtils::SCENARIO_UPDATE;

            // render GUI
            $renderView = 'update';
            $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'arrNetCustomer'=>$arrNetCustomer];
            switch ($submitMode) {
                case MasterValueUtils::SM_MODE_INPUT:
                    $isValid = $model->validate();
                    if ($isValid) {
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_EDIT];
                    }
                    break;
                case MasterValueUtils::SM_MODE_CONFIRM:
                    $isValid = $model->validate();
                    if ($isValid) {
                        $result = $this->updatePayment($model, $fmShortDatePhp);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Payment')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$model->secret_key]));
                        } else {
                            // modify data for View
                            $model->entry_date = DateTimeUtils::parse($model->entry_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                            // render View
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                            $renderView = 'confirm';
                            $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_EDIT];
                        }
                    }
                    break;
                case MasterValueUtils::SM_MODE_BACK:
                    break;
                default:
                    break;
            }
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    /**
     * update a Payment
     * @param $paymentModel
     * @param $fmDateTimePhp
     * @throws Exception
     * @return string|true
     */
    private function updatePayment($paymentModel, $fmDateTimePhp) {
        // modify data for DB
        $paymentModel->entry_date = DateTimeUtils::parse($paymentModel->entry_date, $fmDateTimePhp, DateTimeUtils::FM_DB_DATE);

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save Customer
            $customer = NetCustomer::findOne(['id'=>$paymentModel->customer_id]);
            if ($save !== false && !is_null($customer)) {
                $customer->balance = $customer->balance + $paymentModel->credit - $paymentModel->credit_old;
                $save = $customer->save();
            }

            // save Payment
            if ($paymentModel->entry_date == $paymentModel->entry_date_old) {
                $paymentModel->secret_key = EnDescrypt::encryptSha1($paymentModel->customer_id . $paymentModel->entry_date);
                $save = $paymentModel->save(false);
            } else {
                if ($paymentModel->debit > 0) {
                    // save new Payment
                    $newSecretKey = EnDescrypt::encryptSha1($paymentModel->customer_id . $paymentModel->entry_date);
                    $newPaymentModel = new NetPayment();
                    $newPaymentModel->customer_id = $paymentModel->customer_id;
                    $newPaymentModel->entry_date = $paymentModel->entry_date;
                    $newPaymentModel->credit = $paymentModel->credit;
                    $newPaymentModel->debit = 0;
                    $newPaymentModel->order_id = 0;
                    $newPaymentModel->secret_key = $newSecretKey;
                    $save = $newPaymentModel->save(false);

                    // save old Payment
                    if ($save !== false) {
                        $paymentModel->entry_date = $paymentModel->entry_date_old;
                        $paymentModel->credit = 0;
                        $paymentModel->secret_key = EnDescrypt::encryptSha1($paymentModel->customer_id . $paymentModel->entry_date_old);
                        $save = $paymentModel->save(false);
                        $paymentModel->secret_key = $newSecretKey;
                    }
                } else {
                    $paymentModel->secret_key = EnDescrypt::encryptSha1($paymentModel->customer_id . $paymentModel->entry_date);
                    $save = $paymentModel->save(false);
                }
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Payment')]);
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
}