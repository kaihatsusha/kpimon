<?php
namespace app\modules\fin\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
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
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        FinTotalInterestUnit::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $model = new FinTotalInterestUnit();

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model->load($postData);

        // init value
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
                    $result = $this->createInterestUnit($model, $fmShortDatePhp);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Interest Unit')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        // modify data for View
                        $model->start_date = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                        if (!empty($model->end_date)) {
                            $model->end_date = DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
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
        return $this->render($renderView, $renderData);
    }

    /**
     * create an Interest Unit
     * @param $interestUnitModel
     * @param $fmDateTimePhp
     * @throws Exception
     * @return string|true
     */
    private function createInterestUnit($interestUnitModel, $fmDateTimePhp) {
        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // modify data for DB
            $interestUnitModel->start_date = DateTimeUtils::parse($interestUnitModel->start_date, $fmDateTimePhp, DateTimeUtils::FM_DB_DATE);
            if (!empty($interestUnitModel->end_date)) {
                $interestUnitModel->end_date = DateTimeUtils::parse($interestUnitModel->end_date, $fmDateTimePhp, DateTimeUtils::FM_DB_DATE);
            }
            // save Interest Unit
            $save = $interestUnitModel->save(false);
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Interest Unit')]);
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
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        FinTotalInterestUnit::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $this->objectId = $id;
        $model = FinTotalInterestUnit::findOne(['id'=>$id]);

        $renderView = 'update';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Interest Unit')]));
        } else {
            // master value
            $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
            // modify data for View
            $model->start_date = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
            if (!empty($model->end_date)) {
                $model->end_date = DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
            }

            // submit data
            $postData = Yii::$app->request->post();
            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

            // populate model attributes with user inputs
            $model->load($postData);

            // init value
            $model->scenario = MasterValueUtils::SCENARIO_UPDATE;
            $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui];
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
                        $result = $this->updateInterestUnit($model, $fmShortDatePhp);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Interest Unit')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$id]));
                        } else {
                            // modify data for View
                            $model->start_date = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                            if (!empty($model->end_date)) {
                                $model->end_date = DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
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
     * update an Interest Unit
     * @param $interestUnitModel
     * @param $fmDateTimePhp
     * @throws Exception
     * @return string|true
     */
    private function updateInterestUnit($interestUnitModel, $fmDateTimePhp) {
        return $this->createInterestUnit($interestUnitModel, $fmDateTimePhp);
    }

    public function actionCopy($id) {
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        FinTotalInterestUnit::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $this->objectId = $id;
        $model = FinTotalInterestUnit::findOne(['id'=>$id]);

        $renderView = 'copy';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Interest Unit')]));
        } else {
            // master value
            $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
            // modify data for View
            if (empty($model->end_date)) {
                $model->start_date = DateTimeUtils::formatNow($fmShortDatePhp);
            } else {
                $startDate = DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE);
                $model->start_date = DateTimeUtils::addDateTime($startDate, 'P1D', $fmShortDatePhp);
                $model->end_date = null;
                $model->interest_unit = null;
            }

            // submit data
            $postData = Yii::$app->request->post();
            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

            // populate model attributes with user inputs
            $model->load($postData);

            // init value
            $model->scenario = MasterValueUtils::SCENARIO_COPY;
            $renderData = ['model'=>$model, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui];
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
                        $result = $this->copyInterestUnit($model, $fmShortDatePhp);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Interest Unit')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['index']));
                        } else {
                            // modify data for View
                            $model->start_date = DateTimeUtils::parse($model->start_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                            if (!empty($model->end_date)) {
                                $model->end_date = DateTimeUtils::parse($model->end_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
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
     * copy an Interest Unit
     * @param $interestUnitModel
     * @param $fmDateTimePhp
     * @throws Exception
     * @return string|true
     */
    private function copyInterestUnit($interestUnitModel, $fmDateTimePhp) {
        $interestUnitModel->setIsNewRecord(true);
        return $this->createInterestUnit($interestUnitModel, $fmDateTimePhp);
    }
}