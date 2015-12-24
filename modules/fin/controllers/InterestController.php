<?php
namespace app\modules\fin\controllers;

use Yii;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\NumberUtils;
use app\controllers\MobiledetectController;
use app\models\FinTotalInterestUnit;

class InterestController extends MobiledetectController {
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'copy'],
                'rules' => [
                    [
                        'allow' => true, 'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex() {
        // init value
        $dataQuery = FinTotalInterestUnit::find()->orderBy('id DESC');

        // chart data
        $chartModels = FinTotalInterestUnit::find()->orderBy('id DESC')->limit(30)->all();
        $arrLabelChart = [];
        $arrInterestUnitDataChart = [];
        $arrInterestUnitAliasDataChart = [];
        foreach ($chartModels as $chartModel) {
            $arrLabelChart[] = '';
            $arrInterestUnitDataChart[] = $chartModel->interest_unit;
            $arrInterestUnitAliasDataChart[] = NumberUtils::format($chartModel->interest_unit, 2);
        }
        $arrInterestUnitDataChart = array_reverse($arrInterestUnitDataChart);
        $arrInterestUnitAliasDataChart = array_reverse($arrInterestUnitAliasDataChart);
        $chartData = json_encode(['label'=>$arrLabelChart, 'interestUnit'=>$arrInterestUnitDataChart, 'interestUnitAlias'=>$arrInterestUnitAliasDataChart], JSON_NUMERIC_CHECK);

        // render GUI
        $renderData = ['dataQuery'=>$dataQuery, 'chartData'=>$chartData];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {
        $this->objectId = $id;
        $model = FinTotalInterestUnit::findOne(['id'=>$id]);

        $renderView = 'view';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Interest Unit')]));
        } else {
            // data for rendering
            $renderData = ['model'=>$model];
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    public function actionCreate() {
        $model = new FinTotalInterestUnit();
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model->load($postData);

        // init value
        FinTotalInterestUnit::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $model->scenario = MasterValueUtils::SCENARIO_CREATE;
        if (empty($model->start_date)) {
            $model->start_date = DateTimeUtils::formatNow($fmShortDatePhp);
        }

        // render GUI
        $renderView = 'create';
        $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui];
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
                    /*$result = $this->createPayment($model);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Payment')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
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
        return $this->render($renderView, $renderData);
    }

    public function actionUpdate($id) {
        $this->objectId = $id;
    }

    public function actionCopy($id) {
        $this->objectId = $id;
    }
}