<?php
namespace app\modules\oef\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\OefNav;

class NavController extends MobiledetectController {
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
        OefNav::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $searchModel = new OefNav();

        // submit data
        $postData = Yii::$app->request->post();

        // populate model attributes with user inputs
        $searchModel->load($postData);

        // init value
        $today = DateTimeUtils::getNow();
        if (Yii::$app->request->getIsGet()) {
            $tdInfo = getdate($today->getTimestamp());
            $searchModel->trade_date_to = $today->format($fmShortDatePhp);
            $searchModel->trade_date_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        }
        $searchModel->scenario = MasterValueUtils::SCENARIO_LIST;

        // query for dataprovider
        $dataQuery = null;
        if ($searchModel->validate()) {
            $dataQuery = OefNav::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

            if (!empty($searchModel->trade_date_from)) {
                $dataQuery->andWhere(['>=', 'trade_date', $searchModel->trade_date_from]);
            }
            if (!empty($searchModel->trade_date_to)) {
                $dataQuery->andWhere(['<=', 'trade_date', $searchModel->trade_date_to]);
            }
            $dataQuery->orderBy('trade_date DESC');
        } else {
            $dataQuery = OefNav::find()->where(['nav_id'=>-1]);
        }

        // render GUI
        $renderData = ['searchModel'=>$searchModel, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui, 'dataQuery'=>$dataQuery];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {
        $this->objectId = $id;
        $model = OefNav::findOne(['nav_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

        $renderView = 'view';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('oef.models', 'Nav')]));
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
        OefNav::$_PHP_FM_SHORTDATE = $fmShortDatePhp;

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = new OefNav();
        $model->load($postData);

        // init value
        $model->scenario = MasterValueUtils::SCENARIO_CREATE;

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
                    $result = $this->saveNav($model, $fmShortDatePhp);
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

    public function actionUpdate($id) {
        $this->objectId = $id;
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        OefNav::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $model = OefNav::findOne(['nav_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

        $renderView = 'update';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('oef.models', 'Nav')]));
        } else {
            // submit data
            $postData = Yii::$app->request->post();
            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
            // populate model attributes with user inputs
            $model->load($postData);
            // init value
            $model->scenario = MasterValueUtils::SCENARIO_UPDATE;
            // render GUI
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
                        $result = $this->saveNav($model, $fmShortDatePhp);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('oef.models', 'Nav')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$id]));
                        } else {
                            // restore Data for View
                            $model->trade_date = DateTimeUtils::parse($model->trade_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                            $model->decide_date = DateTimeUtils::parse($model->decide_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
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
     * save Nav
     * @param $nav OefNav
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function saveNav($nav, $fmShortDatePhp) {
        // modify data for DB
        $nav->trade_date = DateTimeUtils::parse($nav->trade_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
        $nav->decide_date = DateTimeUtils::parse($nav->decide_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save Nav
            $save = $nav->save();
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('oef.models', 'Nav')]);
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