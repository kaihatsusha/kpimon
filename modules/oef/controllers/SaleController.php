<?php
namespace app\modules\oef\controllers;

use Yii;
//use yii\base\Exception;
use yii\db\Query;
//use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\ParamUtils;
use app\controllers\MobiledetectController;
use app\models\extended\OefFundCertificateDividend;
use app\models\extended\OefFundCertificateIpo;
use app\models\extended\OefFundCertificateNormal;
use app\models\extended\OefFundCertificateSip;
use app\models\OefFundCertificate;
use app\models\OefNav;
use app\models\OefPurchase;

class SaleController extends MobiledetectController {
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
        // master value
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $fmShortDateJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null);
        $arrPurchaseType = MasterValueUtils::getArrData('oef_purchase_type');
        OefFundCertificate::$_PHP_FM_SHORTDATE = $fmShortDatePhp;

        // submit data
        $postData = Yii::$app->request->post();
        $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

        // populate model attributes with user inputs
        $model = new OefFundCertificate();
        $model->load($postData);

        // init value
        $model->scenario = MasterValueUtils::SCENARIO_TOOL;
        if (Yii::$app->request->getIsGet()) {
            $today = DateTimeUtils::getNow();
            $model->sell_date = $today->format($fmShortDatePhp);
            $model->income_tax_rate = ParamUtils::getIncomeTaxRateSale();

            $sumPurchaseQuery = (new Query())->select(['SUM(found_stock) AS found_stock', 'SUM(found_stock_sold) AS found_stock_sold']);
            $sumPurchaseQuery->from('oef_purchase')->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])->andWhere('found_stock > found_stock_sold');
            $sumPurchaseData = $sumPurchaseQuery->createCommand()->queryOne();
            if (!is_null($sumPurchaseData['found_stock']) && !is_null($sumPurchaseData['found_stock_sold'])) {
                $model->sell_certificate = $sumPurchaseData['found_stock'] - $sumPurchaseData['found_stock_sold'];
            }
        }
        $isValidSellDate = $model->validate(['sell_date']);
        if ($isValidSellDate && empty($model->nav)) {
            $sellDate = DateTimeUtils::parse($model->sell_date, $fmShortDatePhp);
            $oefNavModel = OefNav::findOne(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE, 'trade_date'=>$sellDate->format(DateTimeUtils::FM_DB_DATE)]);
            if (!is_null($oefNavModel)) {
                $model->nav = $oefNavModel->nav_value;
            }
        }

        // render GUI
        $renderView = 'tool';
        $renderData = ['model'=>$model, 'fmShortDateJui'=>$fmShortDateJui, 'arrPurchaseType'=>$arrPurchaseType, 'arrFundCertificate4Sell'=>false];
        switch ($submitMode) {
            case MasterValueUtils::SM_MODE_INPUT:
                $isValid = $model->validate();
                if ($isValid) {
                    $arrFundCertificate4Sell = $this->getFundCertificate4Sell($model, $fmShortDatePhp);
                    $renderData['arrFundCertificate4Sell'] = $arrFundCertificate4Sell;
                }
                break;
            default:
                break;
        }

        // render GUI
        return $this->render($renderView, $renderData);
    }

    /**
     * @param OefFundCertificate $condition
     * @param String $fmShortDatePhp
     * @return array
     */
    private function getFundCertificate4Sell($condition, $fmShortDatePhp) {
        $condition->sum_sell_certificate = 0;
        $condition->investment = 0;
        $condition->sellable_certificate = 0;
        $condition->revenue = 0;
        $condition->sell_fee = 0;
        $condition->profit_before_taxes = 0;
        $condition->income_tax = 0;
        $condition->profit_after_taxes = 0;
        $condition->investment_result = 0;
        $sqlLimit = 3;
        $sqlOffset = 0;
        $results = [];
        $condition->sell_date_obj = DateTimeUtils::parse($condition->sell_date, $fmShortDatePhp);
        $running = true;
        while ($running) {
            $arrOefPurchase = OefPurchase::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                ->andWhere('found_stock > found_stock_sold')
                ->addOrderBy(['purchase_date'=>SORT_ASC])
                ->offset($sqlOffset)->limit($sqlLimit)->all();
            $running = count($arrOefPurchase) > 0;
            if ($running) {
                foreach ($arrOefPurchase as $oefPurchase) {
                    $result = null;
                    switch ($oefPurchase->purchase_type) {
                        case MasterValueUtils::MV_OEF_PERCHASE_TYPE_NORMAL:
                            $result = new OefFundCertificateNormal();
                            break;
                        case MasterValueUtils::MV_OEF_PERCHASE_TYPE_SIP:
                            $result = new OefFundCertificateSip();
                            break;
                        case MasterValueUtils::MV_OEF_PERCHASE_TYPE_DIVIDEND:
                            $result = new OefFundCertificateDividend();
                            break;
                        case MasterValueUtils::MV_OEF_PERCHASE_TYPE_IPO:
                            $result = new OefFundCertificateIpo();
                            break;
                    }
                    if (!is_null($result)) {
                        $result->initialize($oefPurchase, $condition);
                        $results[] = $result;

                        $condition->investment += $result->investment;
                        $condition->sellable_certificate += $result->sellable_certificate;
                        $condition->sum_sell_certificate += $result->sell_certificate;
                        $condition->revenue += $result->revenue;
                        $condition->sell_fee += $result->sell_fee;
                        $condition->profit_before_taxes += $result->profit_before_taxes;
                        $condition->income_tax += $result->income_tax;
                        $condition->profit_after_taxes += $result->profit_after_taxes;
                        $condition->investment_result += $result->investment_result;
                        $running = $condition->sum_sell_certificate < $condition->sell_certificate;
                        if (!$running) {
                            break;
                        }
                    }
                }
                $sqlOffset += $sqlLimit;
            }
        }
        return $results;
    }
}