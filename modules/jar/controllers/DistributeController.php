<?php
namespace app\modules\jar\controllers;

use app\models\JarShareDetail;
use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\JarAccount;
use app\models\JarShare;

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
            $dataQuery->orderBy('share_date DESC');
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
            $arrShareDetail = JarShareDetail::find()->select('d.*, a.account_name, p.entry_value AS share_value')->from('jar_share_detail d, jar_account a, jar_payment p')
                ->where(['d.share_id'=>$id, 'd.delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                ->andWhere('d.account_id = a.account_id AND d.share_id = p.share_id AND a.account_id = p.account_target')
                ->orderBy('a.order_num')->all();

            // data for rendering
            $renderData = ['model'=>$model, 'arrShareDetail'=>$arrShareDetail];
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    public function actionCreate() {

    }

    public function actionUpdate($id) {

    }
}