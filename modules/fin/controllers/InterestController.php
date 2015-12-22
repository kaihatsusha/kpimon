<?php
namespace app\modules\fin\controllers;

use Yii;
use app\components\DateTimeUtils;
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
    }

    public function actionCreate() {

    }

    public function actionUpdate($id) {
        $this->objectId = $id;
    }

    public function actionCopy($id) {
        $this->objectId = $id;
    }
}