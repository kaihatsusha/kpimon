<?php
namespace app\modules\net\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\NetCustomer;

class CustomerController extends MobiledetectController {
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
        $arrCustomerStatus = MasterValueUtils::getArrData('net_customer_status');
        $dataQuery = NetCustomer::find()->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])->orderBy('order_num');

        // render GUI
        $renderData = ['dataQuery'=>$dataQuery, 'arrCustomerStatus'=>$arrCustomerStatus];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {

    }

    public function actionCreate() {
        $model = new NetCustomer();
        $arrCustomerStatus = MasterValueUtils::getArrData('net_customer_status');

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model->load($postData);

        // init value
        if (empty($model->status)) {
            $model->status = MasterValueUtils::MV_NET_CUSTOMER_STATUS_ON;
        }
        $model->scenario = MasterValueUtils::SCENARIO_CREATE;

        // render GUI
        $renderView = 'create';
        $renderData = ['model'=>$model, 'arrCustomerStatus'=>$arrCustomerStatus];
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
                    $result = $this->createCustomer($model);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('net.models', 'Customer')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
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
     * create a Customer
     * @param $customerModel
     * @throws Exception
     * @return string|true
     */
    private function createCustomer($customerModel) {
        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save Customer
            $save = $customerModel->save(false);
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('net.models', 'Customer')]);
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
        $model = NetCustomer::findOne(['id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

        $renderView = 'update';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('net.models', 'Customer')]));
        } else {
            // master value
            $arrCustomerStatus = MasterValueUtils::getArrData('net_customer_status');

            // submit data
            $postData = Yii::$app->request->post();
            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

            // populate model attributes with user inputs
            $model->load($postData);

            // init value
            $model->scenario = MasterValueUtils::SCENARIO_UPDATE;
            $renderData = ['model'=>$model, 'arrCustomerStatus'=>$arrCustomerStatus];
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
                        $result = $this->createCustomer($model);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('net.models', 'Customer')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$id]));
                        } else {
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
}