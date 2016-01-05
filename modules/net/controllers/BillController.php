<?php
namespace app\modules\net\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\ModelUtils;
use app\components\NumberUtils;
use app\components\StringUtils;
use app\controllers\MobiledetectController;
use app\models\NetBill;
use app\models\NetBillDetail;
use app\models\NetCustomer;
use app\models\NetPayment;

class BillController extends MobiledetectController {
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
        $arrNetCustomer = ModelUtils::getArrData(NetCustomer::find()->select(['id', 'name'])
            ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_NET_CUSTOMER_STATUS_ON])
            ->orderBy('order_num'), 'id', 'name');
        NetBill::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $searchModel = new NetBill();

        // submit data
        $postData = Yii::$app->request->post();

        // populate model attributes with user inputs
        $searchModel->load($postData);

        // init value
        $today = DateTimeUtils::getNow();
        if (Yii::$app->request->getIsGet()) {
            $tdInfo = getdate($today->getTimestamp());
            $searchModel->bill_date_to = $today->format($fmShortDatePhp);
            $searchModel->bill_date_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        }
        $searchModel->scenario = MasterValueUtils::SCENARIO_LIST;
        // sum Bill
        $sumBillValue = false;
        // query for dataprovider
        $dataQuery = null;
        if ($searchModel->validate()) {
            $dataQuery = NetBill::find()->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            $sumBillQuery = (new Query())->select(['SUM(total) AS total'])->from('net_bill')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            if (!empty($searchModel->bill_date_from)) {
                $dataQuery->andWhere(['>=', 'bill_date', $searchModel->bill_date_from]);
                $sumBillQuery->andWhere(['>=', 'bill_date', $searchModel->bill_date_from]);
            }
            if (!empty($searchModel->bill_date_to)) {
                $dataQuery->andWhere(['<=', 'bill_date', $searchModel->bill_date_to]);
                $sumBillQuery->andWhere(['<=', 'bill_date', $searchModel->bill_date_to]);
            }
            $dataQuery->orderBy('bill_date DESC');
            $sumBillValue = $sumBillQuery->createCommand()->queryOne();
        } else {
            $dataQuery = NetBill::find()->where(['id'=>-1]);
        }

        // render GUI
        $renderData = ['searchModel'=>$searchModel, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'arrNetCustomer'=>$arrNetCustomer,
            'dataQuery'=>$dataQuery, 'sumBillValue'=>$sumBillValue];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {
        $this->objectId = $id;
        $model = NetBill::findOne(['id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

        $renderView = 'view';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Bill')]));
        } else {
            // master value
            $arrNetCustomer = ModelUtils::getArrData(NetCustomer::find()->select(['id', 'name'])
                ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_NET_CUSTOMER_STATUS_ON])
                ->orderBy('order_num'), 'id', 'name');
            $arrMemberList = StringUtils::unserializeArr($model->member_list);
            $model->member_list = StringUtils::showArrValueAsString($arrMemberList, $arrNetCustomer);
            // Detail of Items
            $arrBillDetail = NetBillDetail::find()->where(['=', 'bill_id', $id])->orderBy('item_no ASC')->all();

            // data for rendering
            $renderData = ['model'=>$model, 'arrBillDetail'=>$arrBillDetail];
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
        NetBill::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        NetBillDetail::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $billDetail = new NetBillDetail();

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = $this->loadBill(null, $postData, $fmShortDatePhp);
        $billDetail->load($postData);
        $arrBillDetail = $this->loadBillDetails(null, $postData, $fmShortDatePhp);
        if (Yii::$app->request->getIsGet()) {
            $model->bill_date = DateTimeUtils::formatNow($fmShortDatePhp);
            $model->arr_member_list = array_keys($arrNetCustomer);
        }

        // init value
        $model->scenario = MasterValueUtils::SCENARIO_CREATE;

        // render GUI
        $renderView = 'create';
        $renderData = ['model'=>$model, 'billDetail'=>$billDetail, 'fmShortDatePhp'=>$fmShortDatePhp, 'arrBillDetail'=>$arrBillDetail,
            'fmShortDateJui'=>$fmShortDateJui, 'arrNetCustomer'=>$arrNetCustomer];
        switch ($submitMode) {
            case MasterValueUtils::SM_MODE_ADD_ITEM:
                $itemNo = empty($billDetail->item_no) ? count($arrBillDetail) + 1 : $billDetail->item_no;
                $billDetail->item_no = $itemNo;
                $arrBillDetail[$itemNo] = $billDetail;
                $isValid = $this->validate($model, $arrBillDetail, false);
                if ($isValid !== true) {
                    $renderData['isValid'] = $isValid;
                }
                $renderData['model'] = $model;
                $renderData['arrBillDetail'] = $arrBillDetail;
                break;
            case MasterValueUtils::SM_MODE_DEL_ITEM:
                $arrBillDetail[$billDetail->item_no]->delete_flag = MasterValueUtils::MV_FIN_FLG_DELETE_TRUE;
                $renderData['arrBillDetail'] = $arrBillDetail;
                break;
            case MasterValueUtils::SM_MODE_INPUT:
                $isValid = $this->validate($model, $arrBillDetail);
                if ($isValid === true) {
                    $renderView = 'confirm';
                    $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                } else {
                    $renderData['isValid'] = $isValid;
                }
                $renderData['model'] = $model;
                $renderData['arrBillDetail'] = $arrBillDetail;
                break;
            case MasterValueUtils::SM_MODE_CONFIRM:
                $isValid = $this->validate($model, $arrBillDetail);
                if ($isValid === true) {
                    $result = $this->createBill($model, $arrBillDetail, $fmShortDatePhp);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Bill')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        // restore Data for View
                        $this->restoreData4View($model, $arrBillDetail, $fmShortDatePhp);
                        // render View
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                    }
                } else {
                    $renderData['isValid'] = $isValid;
                    $renderData['model'] = $model;
                    $renderData['arrBillDetail'] = $arrBillDetail;
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
     * get Basic of Bill
     * @param $billId
     * @param $postData
     * @param $fmShortDatePhp
     * @return NetBill|false
     */
    private function loadBill($billId, $postData, $fmShortDatePhp) {
        $result = null;
        if (is_null($billId)) {
            $result = new NetBill();
            $result->total = 0;
            $result->member_num = 0;
        } else {
            $result = NetBill::findOne(['id'=>$billId, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            if (is_null($result)) {
                return false;
            }
            $result->bill_date = DateTimeUtils::parse($result->bill_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
            $result->arr_member_list = StringUtils::unserializeArr($result->member_list);
            $result->arr_member_list_old = StringUtils::unserializeArr($result->member_list);
        }

        $result->total_old = $result->total;
        $result->member_num_old = $result->member_num;

        $result->load($postData);
        return $result;
    }

    /**
     * get Detail Items of Bill
     * @param $billId
     * @param $postData
     * @param $fmShortDatePhp
     * @return array
     */
    private function loadBillDetails($billId, $postData, $fmShortDatePhp) {
        $result = [];
        if (!is_null($billId)) {
            $temps = NetBillDetail::find()->where(['=', 'bill_id', $billId])->orderBy('item_no ASC')->all();
            foreach ($temps as $temp) {
                $result[$temp->item_no] = $temp;
            }
        }
        if (isset($postData['NetBillDetail'])) {
            foreach ($postData['NetBillDetail'] as $k=>$v) {
                if (is_numeric($k) && is_array($v)) {
                    $item = null;
                    if (isset($result[$k])) {
                        $item = $result[$k];
                        $item->pay_date = DateTimeUtils::parse($item->pay_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                    } else {
                        $item = new NetBillDetail();
                        $item->price = 0;
                    }
                    $item->price_old = $item->price;
                    $item->delete_flag = 0;
                    $item->is_valid = true;
                    $result[$k] = $item;
                }
            }
            NetBillDetail::loadMultiple($result, $postData);
        }
        return $result;
    }

    /**
     * validate input data
     * @param $model
     * @param $arrBillDetail
     * @param $checkModel
     * @return string|true|false
     */
    private function validate($model, $arrBillDetail, $checkModel = true) {
        $isValidModel = ($checkModel) ? $model->validate() : true;
        $isValidItems = true;
        $model->total = 0;
        if (count($arrBillDetail) > 0) {
            $realCount = 0;
            foreach ($arrBillDetail as $billDetail) {
                $billDetail->is_valid = true;
                if ($billDetail->delete_flag) {
                    // do nothing
                } else {
                    $realCount++;
                    $billDetail->scenario = MasterValueUtils::SCENARIO_CREATE;
                    if ($billDetail->validate()) {
                        $model->total += $billDetail->price;
                    } else {
                        $billDetail->is_valid = false;
                        $isValidItems = false;
                    }
                    $billDetail->scenario = null;
                }
            }
            if ($realCount < 1) {
                $isValidItems = Yii::t('common', 'Detail Items List must be at least one item.');
            }
        } else {
            $isValidItems = Yii::t('common', 'Detail Items List must be at least one item.');
        }

        if ($isValidModel && $isValidItems === true) {
            return true;
        }
        if ($isValidItems !== true) {
            return $isValidItems;
        }
        return $isValidModel;
    }

    /**
     * create a Bill
     * @param $model
     * @param $arrBillDetail
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function createBill($model, $arrBillDetail, $fmShortDatePhp) {
        // modify data for DB
        $model->bill_date = DateTimeUtils::parse($model->bill_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
        $model->member_num = count($model->arr_member_list);
        $model->member_list = serialize($model->arr_member_list);
        $pricePerMember = NumberUtils::rounds($model->total / $model->member_num, NumberUtils::NUM_CEIL);
        foreach ($arrBillDetail as $billDetail) {
            if (!$billDetail->delete_flag) {
                $billDetail->pay_date = DateTimeUtils::parse($billDetail->pay_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save Bill
            $save = $model->save(false);
            // save Bill Detail
            if ($save !== false) {
                $itemNo = 1;
                foreach ($arrBillDetail as $billDetail) {
                    if (!$billDetail->delete_flag) {
                        $billDetail->bill_id = $model->id;
                        $billDetail->item_no = $itemNo;
                        $itemNo ++;
                        $save = $billDetail->save(false);
                        if ($save === false) {
                            break;
                        }
                    }
                }
            }
            // save Customer & Payment
            if ($save !== false) {
                foreach ($model->arr_member_list as $customerId) {
                    // save Customer
                    $customer = NetCustomer::findOne($customerId);
                    $customer->balance = $customer->balance - $pricePerMember;
                    $save = $customer->save(false);
                    if ($save === false) {
                        break;
                    }
                    // save Payment
                    $payment = NetPayment::findOne(['customer_id'=>$customerId, 'entry_date'=>$model->bill_date]);
                    if (is_null($payment)) {
                        $payment = new NetPayment();
                        $payment->customer_id = $customerId;
                        $payment->entry_date = $model->bill_date;
                    }
                    $payment->debit = $pricePerMember;
                    $payment->order_id = $model->id;
                    $save = $payment->save(false);
                    if ($save === false) {
                        break;
                    }
                }
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Bill')]);
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

    /**
     * restore Data for View
     * @param $model
     * @param $arrBillDetail
     * @param $fmShortDatePhp
     */
    private function restoreData4View($model, $arrBillDetail, $fmShortDatePhp) {
        $model->bill_date = DateTimeUtils::parse($model->bill_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
        foreach ($arrBillDetail as $billDetail) {
            if (!$billDetail->delete_flag) {
                $billDetail->pay_date = DateTimeUtils::parse($billDetail->pay_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
            }
        }
    }

    public function actionUpdate($id) {
        $this->objectId = $id;
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        NetBill::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        NetBillDetail::$_PHP_FM_SHORTDATE = $fmShortDatePhp;

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = $this->loadBill($id, $postData, $fmShortDatePhp);

        $renderView = 'update';
        if ($model === false) {
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Bill')]));
        } else {
            // master value
            $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
            $arrNetCustomer = ModelUtils::getArrData(NetCustomer::find()->select(['id', 'name'])
                ->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'status'=>MasterValueUtils::MV_NET_CUSTOMER_STATUS_ON])
                ->orderBy('order_num'), 'id', 'name');
            $billDetail = new NetBillDetail();

            // populate model attributes with user inputs
            $billDetail->load($postData);
            $arrBillDetail = $this->loadBillDetails($id, $postData, $fmShortDatePhp);

            // init value
            $model->scenario = MasterValueUtils::SCENARIO_UPDATE;

            $renderData = ['model'=>$model, 'billDetail'=>$billDetail, 'fmShortDatePhp'=>$fmShortDatePhp, 'arrBillDetail'=>$arrBillDetail,
                'fmShortDateJui'=>$fmShortDateJui, 'arrNetCustomer'=>$arrNetCustomer];
            switch ($submitMode) {
                case MasterValueUtils::SM_MODE_ADD_ITEM:
                    $itemNo = empty($billDetail->item_no) ? count($arrBillDetail) + 1 : $billDetail->item_no;
                    $billDetail->item_no = $itemNo;
                    $arrBillDetail[$itemNo] = $billDetail;
                    $isValid = $this->validate($model, $arrBillDetail, false);
                    if ($isValid !== true) {
                        $renderData['isValid'] = $isValid;
                    }
                    $renderData['model'] = $model;
                    $renderData['arrBillDetail'] = $arrBillDetail;
                    break;
                case MasterValueUtils::SM_MODE_DEL_ITEM:
                    $arrBillDetail[$billDetail->item_no]->delete_flag = MasterValueUtils::MV_FIN_FLG_DELETE_TRUE;
                    $renderData['arrBillDetail'] = $arrBillDetail;
                    break;
                case MasterValueUtils::SM_MODE_INPUT:
                    $isValid = $this->validate($model, $arrBillDetail);
                    if ($isValid === true) {
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_EDIT];
                    } else {
                        $renderData['isValid'] = $isValid;
                    }
                    $renderData['model'] = $model;
                    $renderData['arrBillDetail'] = $arrBillDetail;
                    break;
                case MasterValueUtils::SM_MODE_CONFIRM:
                    $isValid = $this->validate($model, $arrBillDetail);
                    if ($isValid === true) {
                        $result = $this->updateBill($model, $arrBillDetail, $fmShortDatePhp);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Bill')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$id]));
                        } else {
                            // restore Data for View
                            $this->restoreData4View($model, $arrBillDetail, $fmShortDatePhp);
                            // render View
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                            $renderView = 'confirm';
                            $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_EDIT];
                        }
                    } else {
                        $renderData['isValid'] = $isValid;
                        $renderData['model'] = $model;
                        $renderData['arrBillDetail'] = $arrBillDetail;
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
     * update a Bill
     * @param $model
     * @param $arrBillDetail
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function updateBill($model, $arrBillDetail, $fmShortDatePhp) {
        // modify data for DB
        $model->bill_date = DateTimeUtils::parse($model->bill_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
        $model->member_num = count($model->arr_member_list);
        $model->member_list = serialize($model->arr_member_list);
        $pricePerMember = NumberUtils::rounds($model->total / $model->member_num, NumberUtils::NUM_CEIL);
        $pricePerMemberOld = NumberUtils::rounds($model->total_old / $model->member_num_old, NumberUtils::NUM_CEIL);
        foreach ($arrBillDetail as $billDetail) {
            if (!$billDetail->delete_flag) {
                $billDetail->pay_date = DateTimeUtils::parse($billDetail->pay_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save Bill
            $save = $model->save(false);
            // save Bill Detail
            if ($save !== false) {
                // delete all Bill Detail
                NetBillDetail::deleteAll(['bill_id'=>$model->id]);
                $itemNo = 1;
                foreach ($arrBillDetail as $billDetail) {
                    if (!$billDetail->delete_flag) {
                        $billDetail->setIsNewRecord(true);
                        $billDetail->bill_id = $model->id;
                        $billDetail->item_no = $itemNo;
                        $itemNo ++;
                        $save = $billDetail->save(false);
                        if ($save === false) {
                            break;
                        }
                    }
                }
            }

            // save Customer & Payment
            if ($save !== false) {
                foreach ($model->arr_member_list_old as $oldCustomerId) {
                    // save old Customer
                    $oldCustomer = NetCustomer::findOne($oldCustomerId);
                    $oldCustomer->balance = $oldCustomer->balance + $pricePerMemberOld;
                    $save = $oldCustomer->save(false);
                    if ($save === false) {
                        break;
                    }
                    // save old Payment
                    $oldPayment = NetPayment::findOne(['customer_id'=>$oldCustomerId, 'order_id'=>$model->id]);
                    if (!is_null($oldPayment)) {
                        if ($oldPayment->credit > 0) {
                            $oldPayment->debit = 0;
                            $oldPayment->order_id = 0;
                            $save = $oldPayment->save(false);
                        } else {
                            $save = $oldPayment->delete();
                        }
                        if ($save === false) {
                            break;
                        }
                    }
                }
                if ($save !== false) {
                    foreach ($model->arr_member_list as $customerId) {
                        // save new Customer
                        $customer = NetCustomer::findOne($customerId);
                        $customer->balance = $customer->balance - $pricePerMember;
                        $save = $customer->save(false);
                        if ($save === false) {
                            break;
                        }
                        // save new Payment
                        $payment = NetPayment::findOne(['customer_id'=>$customerId, 'entry_date'=>$model->bill_date]);
                        if (is_null($payment)) {
                            $payment = new NetPayment();
                            $payment->customer_id = $customerId;
                            $payment->entry_date = $model->bill_date;
                        }
                        $payment->debit = $pricePerMember;
                        $payment->order_id = $model->id;
                        $save = $payment->save(false);
                        if ($save === false) {
                            break;
                        }
                    }
                }
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Bill')]);
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