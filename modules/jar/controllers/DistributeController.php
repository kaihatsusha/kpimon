<?php
namespace app\modules\jar\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\NumberUtils;
use app\controllers\MobiledetectController;
use app\models\JarAccount;
use app\models\JarPayment;
use app\models\JarShare;
use app\models\JarShareDetail;

class DistributeController extends MobiledetectController {
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
        JarShare::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $searchModel = new JarShare();

        // submit data
        $postData = Yii::$app->request->post();

        // populate model attributes with user inputs
        $searchModel->load($postData);

        // init value
        $today = DateTimeUtils::getNow();
        if (Yii::$app->request->getIsGet()) {
            $tdInfo = getdate($today->getTimestamp());
            $searchModel->share_date_to = $today->format($fmShortDatePhp);
            $searchModel->share_date_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        }
        $searchModel->scenario = MasterValueUtils::SCENARIO_LIST;
        // sum Share
        $sumShareValue = false;
        // query for dataprovider
        $dataQuery = null;
        if ($searchModel->validate()) {
            $dataQuery = JarShare::find()->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            $sumShareQuery = (new Query())->select(['SUM(share_value) AS share_value'])->from('jar_share')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
            if (!empty($searchModel->share_date_from)) {
                $searchDate = DateTimeUtils::parse($searchModel->share_date_from, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
                $dataQuery->andWhere(['>=', 'share_date', $searchDate]);
                $sumShareQuery->andWhere(['>=', 'share_date', $searchDate]);
            }
            if (!empty($searchModel->share_date_to)) {
                $searchDate = DateTimeUtils::parse($searchModel->share_date_to, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);
                $dataQuery->andWhere(['<=', 'share_date', $searchDate]);
                $sumShareQuery->andWhere(['<=', 'share_date', $searchDate]);
            }
            $dataQuery->orderBy('share_date DESC, create_date DESC');
            $sumShareValue = $sumShareQuery->createCommand()->queryOne();
        } else {
            $dataQuery = JarShare::find()->where(['share_id'=>-1]);
        }

        // render GUI
        $renderData = ['searchModel'=>$searchModel, 'fmShortDatePhp'=>$fmShortDatePhp, 'fmShortDateJui'=>$fmShortDateJui,
            'dataQuery'=>$dataQuery, 'sumShareValue'=>$sumShareValue];

        return $this->render('index', $renderData);
    }

    public function actionView($id) {
        $this->objectId = $id;
        $model = JarShare::findOne(['share_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

        $renderView = 'view';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('jar.models', 'Shared Item')]));
        } else {
            // Detail of Items
            $arrShareDetail = $this->initShareDetail($model);
            // data for rendering
            $renderData = ['model'=>$model, 'arrShareDetail'=>$arrShareDetail];
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    public function actionCreate() {
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        JarShare::$_PHP_FM_SHORTDATE = $fmShortDatePhp;

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = new JarShare();
        $model->load($postData);
        if (Yii::$app->request->getIsGet()) {
            $model->share_date = DateTimeUtils::formatNow($fmShortDatePhp);
        }

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
                    $renderData['arrShareDetail'] = $this->initShareDetail($model);
                }
                break;
            case MasterValueUtils::SM_MODE_CONFIRM:
                $isValid = $model->validate();
                if ($isValid) {
                    $arrShareDetail = $this->initShareDetail($model);
                    $result = $this->createDistribute($model, $arrShareDetail, $fmShortDatePhp);
                    if ($result === true) {
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('jar.models', 'Distribute')]));
                        return Yii::$app->getResponse()->redirect(Url::to(['index']));
                    } else {
                        // restore Data for View
                        $model->share_date = DateTimeUtils::parse($model->share_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                        // render View
                        Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                        $renderView = 'confirm';
                        $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_CREATE];
                        $renderData['arrShareDetail'] = $arrShareDetail;
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
     * create Distribute
     * @param $share JarShare
     * @param $arrShareDetail JarShareDetail
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function createDistribute($share, $arrShareDetail, $fmShortDatePhp) {
        // modify data for DB
        $share->share_date = DateTimeUtils::parse($share->share_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save JarShare
            $save = $share->save();
            if ($save !== false) {
                foreach ($arrShareDetail as $shareDetail) {
                    // save JarAccount
                    $account = JarAccount::findOne($shareDetail->account_id);
                    $account->real_balance += $shareDetail->share_value;
                    $account->useable_balance += $shareDetail->share_value;
                    $save = $account->save();
                    if ($save === false) {
                        break;
                    }
                    // save JarShareDetail
                    $shareDetail->share_id = $share->share_id;
                    $save = $shareDetail->save();
                    if ($save === false) {
                        break;
                    }
                    // save JarPayment
                    $payment = new JarPayment();
                    $payment->entry_date = $share->share_date;
                    $payment->entry_value = $shareDetail->share_value;
                    $payment->account_source = MasterValueUtils::MV_JAR_ACCOUNT_NONE;
                    $payment->account_target = $shareDetail->account_id;
                    $payment->share_id = $shareDetail->share_id;
                    $payment->entry_status = MasterValueUtils::MV_JAR_ENTRY_TYPE_SIMPLE;
                    $payment->description = $share->description;
                    $save = $payment->save();
                    if ($save === false) {
                        break;
                    }
                }
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('jar.models', 'Distribute')]);
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
        JarShare::$_PHP_FM_SHORTDATE = $fmShortDatePhp;
        $model = JarShare::findOne(['share_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

        $renderView = 'update';
        if (is_null($model)) {
            $model = false;
            $renderData = ['model'=>$model];
            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('jar.models', 'Shared Item')]));
        } else {
            // back up data
            $model->share_value_old = $model->share_value;
            $model->share_date = DateTimeUtils::parse($model->share_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
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
                        $renderData['arrShareDetail'] = $this->initShareDetail($model, true);
                    }
                    break;
                case MasterValueUtils::SM_MODE_CONFIRM:
                    $isValid = $model->validate();
                    if ($isValid) {
                        $arrShareDetail = $this->initShareDetail($model, true);
                        $result = $this->updateDistribute($model, $arrShareDetail, $fmShortDatePhp);
                        if ($result === true) {
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('jar.models', 'Distribute')]));
                            return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$id]));
                        } else {
                            // restore Data for View
                            $model->share_date = DateTimeUtils::parse($model->share_date, DateTimeUtils::FM_DB_DATE, $fmShortDatePhp);
                            // render View
                            Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
                            $renderView = 'confirm';
                            $renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_EDIT];
                            $renderData['arrShareDetail'] = $arrShareDetail;
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
     * update Distribute
     * @param $share JarShare
     * @param $arrShareDetail JarShareDetail
     * @param $fmShortDatePhp
     * @throws Exception
     * @return string|true
     */
    private function updateDistribute($share, $arrShareDetail, $fmShortDatePhp) {
        // modify data for DB
        $share->share_date = DateTimeUtils::parse($share->share_date, $fmShortDatePhp, DateTimeUtils::FM_DB_DATE);

        $transaction = Yii::$app->db->beginTransaction();
        $save = true;
        $message = null;

        // begin transaction
        try {
            // save JarShare
            $save = $share->save();
            if ($save !== false) {
                foreach ($arrShareDetail as $shareDetail) {
                    // save JarAccount
                    $account = JarAccount::findOne($shareDetail->account_id);
                    $account->real_balance = $account->real_balance - $shareDetail->share_value_old + $shareDetail->share_value;
                    $account->useable_balance = $account->useable_balance - $shareDetail->share_value_old + $shareDetail->share_value;
                    $save = $account->save();
                    if ($save === false) {
                        break;
                    }
                    // save JarPayment
                    $payment = JarPayment::findOne(['account_target'=>$shareDetail->account_id, 'share_id'=>$share->share_id]);
                    $payment->entry_date = $share->share_date;
                    $payment->entry_value = $shareDetail->share_value;
                    $payment->description = $share->description;
                    $save = $payment->save();
                    if ($save === false) {
                        break;
                    }
                }
            }
        } catch(Exception $e) {
            $save = false;
            $message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('jar.models', 'Distribute')]);
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
     * get list of JarShareDetail
     * @param $share JarShare
     * @param $backup boolean
     * @return array JarShareDetail
     */
    private function initShareDetail($share, $backup = false) {
        $results = null;
        if (is_null($share->share_id)) {
            $results = [];
            $arrAccount = JarAccount::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE,
                'account_type'=>MasterValueUtils::MV_JAR_ACCOUNT_TYPE_JAR,
                'status'=>MasterValueUtils::MV_JAR_ACCOUNT_STATUS_ON])->orderBy('order_num')->all();
            $per = (1.0 * $share->share_value) / 100;
            foreach ($arrAccount as $account) {
                $item = new JarShareDetail();
                $item->account_id = $account->account_id;
                $item->account_name = $account->account_name;
                $item->share_unit = $account->share_unit;
                $item->share_value = NumberUtils::rounds($per * $account->share_unit);
                $results[] = $item;
            }
        } else {
            $results = JarShareDetail::find()->select('d.*, a.account_name, p.entry_value AS share_value')->from('jar_share_detail d, jar_account a, jar_payment p')
                    ->where(['d.share_id'=>$share->share_id, 'd.delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                    ->andWhere('d.account_id = a.account_id AND d.share_id = p.share_id AND a.account_id = p.account_target')
                    ->orderBy('a.order_num')->all();
            if ($backup) {
                $per = (1.0 * $share->share_value) / 100;
                foreach ($results as $result) {
                    $result->share_value_old = $result->share_value;
                    $result->share_value = NumberUtils::rounds($per * $result->share_unit);
                }
            }
        }

        return $results;
    }
}