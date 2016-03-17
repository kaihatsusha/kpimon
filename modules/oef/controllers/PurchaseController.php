<?php
namespace app\modules\oef\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
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

    }

    public function actionIndex() {

    }

    public function actionView($id) {

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
                if ($isValid) {
                    $renderView = 'confirm';
                    $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                }
                break;
            case MasterValueUtils::SM_MODE_CONFIRM:
                $isValid = $model->validate();
                if ($isValid) {
                    /*$result = $this->saveNav($model, $fmShortDatePhp);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('oef.models', 'Nav')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        // restore Data for View
                        $model->trade_date = DateTimeUtils::parse($model->trade_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                        $model->decide_date = DateTimeUtils::parse($model->decide_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                        // render View
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                    }*/
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

    public function actionUpdate($id) {

    }
}