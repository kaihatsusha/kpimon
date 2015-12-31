<?php
namespace app\modules\net\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\ModelUtils;
use app\controllers\MobiledetectController;
use app\models\NetBill;
use app\models\NetBillDetail;
use app\models\NetCustomer;

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

        // query for dataprovider
        $dataQuery = null;
        if ($searchModel->validate()) {
            $billDateTo = DateTimeUtils::parse($searchModel->bill_date_to, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
            $billDateFrom = DateTimeUtils::parse($searchModel->bill_date_from, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
            $dataQuery = NetBill::find();
        } else {
            $dataQuery = NetBill::find()->where(['id'=>-1]);
        }

        // render GUI
        $renderData = ['searchModel'=>$searchModel, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'dataQuery'=>$dataQuery];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {

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
        $model = new NetBill();
        $billDetail = new NetBillDetail();
        $arrBillDetail = [];

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model->load($postData);
        $billDetail->load($postData);
        if (isset($postData['NetBillDetail'])) {
            foreach ($postData['NetBillDetail'] as $k=>$v) {
                if (is_numeric($k) && is_array($v)) {
                    $arrBillDetail[$k] = new NetBillDetail();
                }
            }
            NetBillDetail::loadMultiple($arrBillDetail, $postData);
        }
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
                    /*$result = $this->createPayment($model);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Payment')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                    }*/
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

    public function actionUpdate($id) {

    }
}