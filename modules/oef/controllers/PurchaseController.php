<?php
namespace app\modules\oef\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\FinAccount;
use app\models\FinAccountEntry;
use app\models\JarAccount;
use app\models\JarPayment;
use app\models\OefPurchase;

class PurchaseController extends MobiledetectController {
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['tool', 'index', 'view', 'create', 'update'],
                'rules' => [
                    [
                        'allow' => true, 'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    public function actionTool() {
        // master value
        $arrPurchaseType = MasterValueUtils::getArrData('oef_purchase_type');

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = new OefPurchase();
        $model->load($postData);

        // init value
        $model->scenario = MasterValueUtils::SCENARIO_TOOL;

        // render GUI
        $renderView = 'tool';
        $renderData = ['model'=>$model, 'arrPurchaseType'=>$arrPurchaseType];
        switch ($submitMode) {
            case MasterValueUtils::SM_MODE_INPUT:
                $isValid = $model->validate();
                if ($isValid) {
                    $model->calculate();
                }
                break;
            default:
                break;
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    public function actionIndex() {
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        $arrPurchaseType = MasterValueUtils::getArrData('oef_purchase_type');
        OefPurchase::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $searchModel = new OefPurchase();

        // submit data
        $postData = Yii::$app->request->post();

        // populate model attributes with user inputs
        $searchModel->load($postData);

        // init value
        $today = DateTimeUtils::getNow();
        if (Yii::$app->request->getIsGet()) {
            $tdInfo = getdate($today->getTimestamp());
            $searchModel->purchase_date_to = $today->format($fmShortDatePhp);
            $searchModel->purchase_date_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        }
        $searchModel->scenario = MasterValueUtils::SCENARIO_LIST;

        // sum purchase, fee, stock
        $sumPurchaseValue = false;
        // query for dataprovider
        $dataQuery = null;
        if ($searchModel->validate()) {
            $dataQuery = OefPurchase::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            $sumPurchaseQuery = (new Query())->select(['SUM(purchase) AS purchase', 'SUM(purchase_fee) AS purchase_fee',
                'SUM(found_stock_sold) AS found_stock_sold', 'SUM(found_stock) AS found_stock', 'SUM(transfer_fee) AS transfer_fee',
                'SUM(other_fee) AS other_fee'])->from('oef_purchase')->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

            if (!empty($searchModel->purchase_date_from)) {
                $dataQuery->andWhere(['>=', 'purchase_date', $searchModel->purchase_date_from]);
                $sumPurchaseQuery->andWhere(['>=', 'purchase_date', $searchModel->purchase_date_from]);
            }
            if (!empty($searchModel->purchase_date_to)) {
                $dataQuery->andWhere(['<=', 'purchase_date', $searchModel->purchase_date_to]);
                $sumPurchaseQuery->andWhere(['<=', 'purchase_date', $searchModel->purchase_date_to]);
            }
            $dataQuery->orderBy('purchase_date DESC');
            $sumPurchaseValue = $sumPurchaseQuery->createCommand()->queryOne();
        } else {
            $dataQuery = OefNav::find()->where(['id'=>-1]);
        }

        // render GUI
        $renderData = ['searchModel'=>$searchModel, 'fmShortDateJui'=>$fmShortDateJui, 'dataQuery'=>$dataQuery,
            'sumPurchaseValue'=>$sumPurchaseValue, 'arrPurchaseType'=>$arrPurchaseType];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {
        $this->objectId = $id;
        $model = OefPurchase::getModel($id);

        $renderView = 'view';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('oef.models', 'Purchase')]));
        } else {
            // master value
            $arrPurchaseType = MasterValueUtils::getArrData('oef_purchase_type');
            // data for rendering
            $renderData = ['model'=>$model, 'arrPurchaseType'=>$arrPurchaseType];
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    public function actionCreate() {
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        $arrPurchaseType = MasterValueUtils::getArrData('oef_purchase_type');
        OefPurchase::$_PHP_FM_SHORTDATE = $fmShortDatePhp;

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = new OefPurchase();
        $model->load($postData);

        // init value
        $model->scenario = MasterValueUtils::SCENARIO_CREATE;

        // render GUI
        $renderView = 'create';
        $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'arrPurchaseType'=>$arrPurchaseType];
        switch ($submitMode) {
            case MasterValueUtils::SM_MODE_INPUT:
                $isValid = $model->validate();
                $model->calculate();
                if ($isValid) {
                    $renderView = 'confirm';
                    $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                }
                break;
            case MasterValueUtils::SM_MODE_CONFIRM:
                $isValid = $model->validate();
                if ($isValid) {
                    $model->calculate();
                    $result = $this->createPurchase($model, $fmShortDatePhp);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('oef.models', 'Purchase')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        // restore Data for View
                        $model->purchase_date = DateTimeUtils::parse($model->purchase_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                        if (!empty($model->sip_date)) {
                            $model->sip_date = DateTimeUtils::parse($model->sip_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                        }
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
     * create a Purchase
     * @param $purchase OefPurchase
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function createPurchase($purchase, $fmShortDatePhp) {
        // modify data for DB
        $purchase->purchase_date = DateTimeUtils::parse($purchase->purchase_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
        if (!empty($purchase->sip_date)) {
            $purchase->sip_date = DateTimeUtils::parse($purchase->sip_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // FinAccountEntry
            $finPayment = new FinAccountEntry();
            $finPayment->entry_date = $purchase->purchase_date;
            $finPayment->entry_value = $purchase->investment;
            $finPayment->account_source = MasterValueUtils::MV_FIN_ACCOUNT_ATM_VCB;
            $finPayment->account_target = MasterValueUtils::MV_FIN_ACCOUNT_VCBF_TBF;
            $finPayment->entry_status = MasterValueUtils::MV_FIN_ENTRY_TYPE_INTEREST_VCBF_TBF;
            $finPayment->description = serialize([MasterValueUtils::MV_FIN_ENTRY_LOG_TRANSFER]);
            $save = $finPayment->save();

            // save FinAccount (Debit)
            if ($save !== false) {
                $debitFinAccount = FinAccount::findOne($finPayment->account_source);
                $debitFinAccount->opening_balance = $debitFinAccount->opening_balance - $purchase->investment;
                $save = $debitFinAccount->save();
            }

            // save FinAccount (Credit)
            if ($save !== false) {
                $creditFinAccount = FinAccount::findOne($finPayment->account_target);
                $creditFinAccount->opening_balance = $creditFinAccount->opening_balance + $purchase->investment;
                $creditFinAccount->capital = $creditFinAccount->capital + $purchase->investment;
                $save = $creditFinAccount->save();
            }

            // save JarPayment
            $jarPayment = new JarPayment();
            if ($save !== false) {
                $jarPayment->entry_date = $purchase->purchase_date;
                $jarPayment->entry_value = $purchase->investment;
                $jarPayment->account_source = MasterValueUtils::MV_JAR_ACCOUNT_LTSS;
                $jarPayment->account_target = MasterValueUtils::MV_JAR_ACCOUNT_TEMP;
                $jarPayment->share_id = MasterValueUtils::MV_JAR_ACCOUNT_NONE;
                $jarPayment->description = 'VCBF-TBF';
                $jarPayment->entry_status = MasterValueUtils::MV_JAR_ENTRY_TYPE_TEMP;
                $save = $jarPayment->save();
            }

            // save JarAccount (Debit)
            if ($save !== false) {
                $debitJarAccount = JarAccount::findOne($jarPayment->account_source);
                $debitJarAccount->useable_balance = $debitJarAccount->useable_balance - $jarPayment->entry_value;
                $save = $debitJarAccount->save();
            }

            // save JarAccount (Credit)
            if ($save !== false) {
                $creditJarAccount = JarAccount::findOne($jarPayment->account_target);
                $creditJarAccount->useable_balance = $creditJarAccount->useable_balance + $jarPayment->entry_value;
                $save = $creditJarAccount->save();
            }

            // save OefPurchase
            if ($save !== false) {
                $purchase->fin_entry_id = $finPayment->entry_id;
                $purchase->jar_payment_id = $jarPayment->id;
                $save = $purchase->save();
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('oef.models', 'Purchase')]);
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
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        OefPurchase::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $model = OefPurchase::getModel($id);

        $renderView = 'update';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('oef.models', 'Purchase')]));
        } else {
            // master value
            $arrPurchaseType = MasterValueUtils::getArrData('oef_purchase_type');
            // submit data
            $postData = Yii::$app->request->post();
            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
            // populate model attributes with user inputs
            $model->load($postData);
            // init value
            $model->scenario = MasterValueUtils::SCENARIO_UPDATE;
            // render GUI
            $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'arrPurchaseType'=>$arrPurchaseType];
            switch ($submitMode) {
                case MasterValueUtils::SM_MODE_INPUT:
                    $isValid = $model->validate();
                    $model->calculate();
                    if ($isValid) {
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_EDIT];
                    }
                    break;
                case MasterValueUtils::SM_MODE_CONFIRM:
                    $isValid = $model->validate();
                    if ($isValid) {
                        $model->calculate();
                        $result = $this->updatePurchase($model, $fmShortDatePhp);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('oef.models', 'Purchase')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$id]));
                        } else {
                            // restore Data for View
                            $model->purchase_date = DateTimeUtils::parse($model->purchase_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                            if (!empty($model->sip_date)) {
                                $model->sip_date = DateTimeUtils::parse($model->sip_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                            }
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
     * update a Purchase
     * @param $purchase OefPurchase
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function updatePurchase($purchase, $fmShortDatePhp) {
        // modify data for DB
        $purchase->purchase_date = DateTimeUtils::parse($purchase->purchase_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
        if (!empty($purchase->sip_date)) {
            $purchase->sip_date = DateTimeUtils::parse($purchase->sip_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save OefPurchase
            $save = $purchase->save();

            // FinAccountEntry
            $finPayment = FinAccountEntry::findOne($purchase->fin_entry_id);
            if ($save !== false && !is_null($finPayment)) {
                $finPayment->entry_date = $purchase->purchase_date;
                $finPayment->entry_value = $purchase->investment;
                $save = $finPayment->save();

                // save FinAccount (Debit)
                if ($save !== false) {
                    $debitFinAccount = FinAccount::findOne($finPayment->account_source);
                    $debitFinAccount->opening_balance = $debitFinAccount->opening_balance - $purchase->investment + $purchase->investment_old;
                    $save = $debitFinAccount->save();
                }

                // save FinAccount (Credit)
                if ($save !== false) {
                    $creditFinAccount = FinAccount::findOne($finPayment->account_target);
                    $creditFinAccount->opening_balance = $creditFinAccount->opening_balance + $purchase->investment - $purchase->investment_old;
                    $creditFinAccount->capital = $creditFinAccount->capital + $purchase->investment - $purchase->investment_old;
                    $save = $creditFinAccount->save();
                }
            }

            // save JarPayment
            $jarPayment = JarPayment::findOne($purchase->jar_payment_id);
            if ($save !== false && !is_null($jarPayment)) {
                $jarPayment->entry_date = $purchase->purchase_date;
                $jarPayment->entry_value = $purchase->investment;
                $save = $jarPayment->save();

                // save JarAccount (Debit)
                if ($save !== false) {
                    $debitJarAccount = JarAccount::findOne($jarPayment->account_source);
                    $debitJarAccount->useable_balance = $debitJarAccount->useable_balance - $purchase->investment + $purchase->investment_old;
                    $save = $debitJarAccount->save();
                }

                // save JarAccount (Credit)
                if ($save !== false) {
                    $creditJarAccount = JarAccount::findOne($jarPayment->account_target);
                    $creditJarAccount->useable_balance = $creditJarAccount->useable_balance + $purchase->investment - $purchase->investment_old;
                    $save = $creditJarAccount->save();
                }
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('oef.models', 'Purchase')]);
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