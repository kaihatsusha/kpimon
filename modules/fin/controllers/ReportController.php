<?php
namespace app\modules\fin\controllers;

use app\components\NumberUtils;
use Yii;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\FinTotalEntryMonth;
use app\models\FinTotalInterestMonth;

class ReportController extends MobiledetectController {
    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['payment', 'deposit', 'assets'],
                'rules' => [
                    [
                        'allow' => true, 'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    public function actionPayment() {
        $model = new FinTotalEntryMonth();
        $fmKeyPhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null, DateTimeUtils::FM_KEY_FMONTH);
        $fmKeyJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null, DateTimeUtils::FM_KEY_FMONTH);

        // is get page than reset value
        if (Yii::$app->request->getIsGet()) {
            $td = DateTimeUtils::getNow();
            $tdTimestamp = $td->getTimestamp();
            $tdInfo = getdate($tdTimestamp);
            $thismonth = $td->format($fmKeyPhp);

            // for report Model
            $model->fmonth = $thismonth;

            // for search Model
            $model->fmonth_to = $thismonth;
            $model->fmonth_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
        } else {
            // submit data
            $postData = Yii::$app->request->post();
            $model->load($postData);

            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
            switch ($submitMode) {
                case MasterValueUtils::SM_MODE_INPUT:
                    $reportMonthInfo = getdate(DateTimeUtils::parse($model->fmonth, $fmKeyPhp)->getTimestamp());
                    $year = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
                    $month = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
                    $reportModel = FinTotalEntryMonth::findOne(['year'=>$year, 'month'=>$month, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    if (is_null($reportModel)) {
                        $reportModel = new FinTotalEntryMonth();
                        $reportModel->year = $year;
                        $reportModel->month = $month;
                    }

                    $reportMonth = DateTimeUtils::parse($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01', DateTimeUtils::FM_DEV_DATE);
                    $fromDate = $reportMonth->format(DateTimeUtils::FM_DB_DATE);
                    $toDate = DateTimeUtils::addDateTime($reportMonth, 'P1M', DateTimeUtils::FM_DB_DATE);

                    $sumEntryQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
                    $sumEntryQuery->from('fin_account_entry')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    $sumEntryQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_FIN_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_FIN_ACCOUNT_NONE]]);
                    $sumEntryQuery->andWhere(['>=', 'entry_date', $fromDate]);
                    $sumEntryQuery->andWhere(['<', 'entry_date', $toDate]);

                    $sumEntryValue = $sumEntryQuery->createCommand()->queryOne();
                    $reportModel->value_out = is_null($sumEntryValue['debit']) ? 0 : $sumEntryValue['debit'];
                    $reportModel->value_in = is_null($sumEntryValue['credit']) ? 0 : $sumEntryValue['credit'];

                    $reportModel->save();

                    Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', 'Monthly Payment Report of {month} has been saved successfully.', ['month'=>$model->fmonth]));
                    return Yii::$app->getResponse()->redirect(Url::to(['payment']));
                    break;
                default:
                    break;
            }
        }
        $renderData = ['fmKeyJui'=>$fmKeyJui, 'fmKeyPhp'=>$fmKeyPhp, 'model'=>$model];

        $fMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_from, $fmKeyPhp)->getTimestamp());
        $tMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_to, $fmKeyPhp)->getTimestamp());
        $fYear = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $fMonth = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $fMonthMM = str_pad($fMonth, 2, '0', STR_PAD_LEFT);
        $tYear = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $tMonth = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $tMonthMM = str_pad($tMonth, 2, '0', STR_PAD_LEFT);

        $gridData = null;
        $resultModel = FinTotalEntryMonth::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
            ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
            ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
            ->orderBy('year, month')->all();
        if (count($resultModel) > 0) {
            // Init data for chart
            $sMonth = $fYear . $fMonthMM . '01';
            $eMonth = $tYear . $tMonthMM . '01';
            $currentMonthObj = DateTimeUtils::parse($sMonth, DateTimeUtils::FM_DEV_DATE);
            $arrDataChartTemp = [];
            while ($sMonth < $eMonth) {
                $sMonth = $currentMonthObj->format(DateTimeUtils::FM_DEV_DATE);
                $arrDataChartTemp[$sMonth] = ['credit'=>0, 'debit'=>0, 'balance'=>0];

                DateTimeUtils::addDateTime($currentMonthObj, 'P1M', null, false);
            }

            $firstResult = $resultModel[0];
            $prevCredit = $firstResult->value_in;
            $prevDebit = $firstResult->value_out;
            $prevBalance = $prevCredit - $prevDebit;

            $gridData = [];
            foreach ($resultModel as $rm) {
                $key = $rm->year . str_pad($rm->month, 2, '0', STR_PAD_LEFT) . '01';
                $balance = $rm->value_in - $rm->value_out;
                $compareCredit = $rm->value_in - $prevCredit;
                $compareDebit = $rm->value_out - $prevDebit;
                $compareBalance = $balance - $prevBalance;

                $prevCredit = $rm->value_in;
                $prevDebit = $rm->value_out;
                $prevBalance = $prevCredit - $prevDebit;

                $girdRow = ['month'=>DateTimeUtils::parse($key, DateTimeUtils::FM_DEV_DATE),
                    'credit'=>$prevCredit, 'debit'=>$prevDebit, 'balance'=>$prevBalance,
                    'compareCredit'=>$compareCredit, 'compareDebit'=>$compareDebit, 'compareBalance'=>$compareBalance];
                $gridData[$key] = $girdRow;

                // data for chart
                if (isset($arrDataChartTemp[$key])) {
                    $arrDataChartTemp[$key]['credit'] = $prevCredit;
                    $arrDataChartTemp[$key]['debit'] = $prevDebit;
                    $arrDataChartTemp[$key]['balance'] = $prevBalance;
                }
            }
            // Total & Average
            $sumGridData = (new Query())->select(['SUM(value_in) AS sum_credit', 'AVG(value_in) AS avg_credit', 'SUM(value_out) AS sum_debit', 'AVG(value_out) AS avg_debit'])
                ->from('fin_total_entry_month')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
                ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
                ->createCommand()->queryOne();
            $renderData['sumGridData'] = $sumGridData;
            // data for chart
            $arrLabelChart = [];
            $arrCreditDataChart = [];
            $arrDebitDataChart = [];
            $arrBalanceDataChart = [];
            $arrCreditAliasDataChart = [];
            $arrDebitAliasDataChart = [];
            $arrBalanceAliasDataChart = [];
            foreach ($arrDataChartTemp as $labelChart=>$dataChartTemp) {
                $arrLabelChart[] = DateTimeUtils::parse($labelChart, DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
                $arrCreditDataChart[] = $dataChartTemp['credit'];
                $arrDebitDataChart[] = $dataChartTemp['debit'];
                $arrBalanceDataChart[] = $dataChartTemp['balance'];

                $arrCreditAliasDataChart[] = NumberUtils::format($dataChartTemp['credit']);
                $arrDebitAliasDataChart[] = NumberUtils::format($dataChartTemp['debit']);
                $arrBalanceAliasDataChart[] = NumberUtils::format($dataChartTemp['balance']);
            }
            $renderData['chartData'] = json_encode(['label'=>$arrLabelChart, 'credit'=>$arrCreditDataChart, 'creditAlias'=>$arrCreditAliasDataChart,
                'debit'=>$arrDebitDataChart, 'debitAlias'=>$arrDebitAliasDataChart,
                'balance'=>$arrBalanceDataChart, 'balanceAlias'=>$arrBalanceAliasDataChart], JSON_NUMERIC_CHECK);
        }

        $renderData['gridData'] = $gridData;
        return $this->render('payment', $renderData);
    }

    public function actionDeposit() {
        $model = new FinTotalInterestMonth();
        $fmKeyPhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null, DateTimeUtils::FM_KEY_FMONTH);
        $fmKeyJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null, DateTimeUtils::FM_KEY_FMONTH);

        // is get page than reset value
        if (Yii::$app->request->getIsGet()) {
            $td = DateTimeUtils::getNow();
            $tdTimestamp = $td->getTimestamp();
            $tdInfo = getdate($tdTimestamp);
            $thismonth = $td->format($fmKeyPhp);

            // for report Model
            $model->fmonth = $thismonth;

            // for search Model
            $model->fmonth_to = $thismonth;
            $model->fmonth_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
        } else {
            // submit data
            $postData = Yii::$app->request->post();
            $model->load($postData);

            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
            switch ($submitMode) {
                case MasterValueUtils::SM_MODE_INPUT:
                    /*$reportMonthInfo = getdate(DateTimeUtils::parse($model->fmonth, $fmKeyPhp)->getTimestamp());
                    $year = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
                    $month = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
                    $reportModel = FinTotalEntryMonth::findOne(['year'=>$year, 'month'=>$month, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    if (is_null($reportModel)) {
                        $reportModel = new FinTotalEntryMonth();
                        $reportModel->year = $year;
                        $reportModel->month = $month;
                    }

                    $reportMonth = DateTimeUtils::parse($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01', DateTimeUtils::FM_DEV_DATE);
                    $fromDate = $reportMonth->format(DateTimeUtils::FM_DB_DATE);
                    $toDate = DateTimeUtils::addDateTime($reportMonth, 'P1M', DateTimeUtils::FM_DB_DATE);

                    $sumEntryQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
                    $sumEntryQuery->from('fin_account_entry')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    $sumEntryQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_FIN_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_FIN_ACCOUNT_NONE]]);
                    $sumEntryQuery->andWhere(['>=', 'entry_date', $fromDate]);
                    $sumEntryQuery->andWhere(['<', 'entry_date', $toDate]);

                    $sumEntryValue = $sumEntryQuery->createCommand()->queryOne();
                    $reportModel->value_out = is_null($sumEntryValue['debit']) ? 0 : $sumEntryValue['debit'];
                    $reportModel->value_in = is_null($sumEntryValue['credit']) ? 0 : $sumEntryValue['credit'];

                    $reportModel->save();

                    Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', 'Monthly Payment Report of {month} has been saved successfully.', ['month'=>$model->fmonth]));
                    return Yii::$app->getResponse()->redirect(Url::to(['payment']));
                    */
                    break;
                default:
                    break;
            }
        }
        $renderData = ['fmKeyJui'=>$fmKeyJui, 'fmKeyPhp'=>$fmKeyPhp, 'model'=>$model];

        $fMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_from, $fmKeyPhp)->getTimestamp());
        $tMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_to, $fmKeyPhp)->getTimestamp());
        $fYear = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $fMonth = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $fMonthMM = str_pad($fMonth, 2, '0', STR_PAD_LEFT);
        $tYear = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $tMonth = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $tMonthMM = str_pad($tMonth, 2, '0', STR_PAD_LEFT);

        $gridData = null;
        /*$resultModel = FinTotalEntryMonth::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
            ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
            ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
            ->orderBy('year, month')->all();
        if (count($resultModel) > 0) {
            // Init data for chart
            $sMonth = $fYear . $fMonthMM . '01';
            $eMonth = $tYear . $tMonthMM . '01';
            $currentMonthObj = DateTimeUtils::parse($sMonth, DateTimeUtils::FM_DEV_DATE);
            $arrDataChartTemp = [];
            while ($sMonth < $eMonth) {
                $sMonth = $currentMonthObj->format(DateTimeUtils::FM_DEV_DATE);
                $arrDataChartTemp[$sMonth] = ['credit'=>0, 'debit'=>0, 'balance'=>0];

                DateTimeUtils::addDateTime($currentMonthObj, 'P1M', null, false);
            }

            $firstResult = $resultModel[0];
            $prevCredit = $firstResult->value_in;
            $prevDebit = $firstResult->value_out;
            $prevBalance = $prevCredit - $prevDebit;

            $gridData = [];
            foreach ($resultModel as $rm) {
                $key = $rm->year . str_pad($rm->month, 2, '0', STR_PAD_LEFT) . '01';
                $balance = $rm->value_in - $rm->value_out;
                $compareCredit = $rm->value_in - $prevCredit;
                $compareDebit = $rm->value_out - $prevDebit;
                $compareBalance = $balance - $prevBalance;

                $prevCredit = $rm->value_in;
                $prevDebit = $rm->value_out;
                $prevBalance = $prevCredit - $prevDebit;

                $girdRow = ['month'=>DateTimeUtils::parse($key, DateTimeUtils::FM_DEV_DATE),
                    'credit'=>$prevCredit, 'debit'=>$prevDebit, 'balance'=>$prevBalance,
                    'compareCredit'=>$compareCredit, 'compareDebit'=>$compareDebit, 'compareBalance'=>$compareBalance];
                $gridData[$key] = $girdRow;

                // data for chart
                if (isset($arrDataChartTemp[$key])) {
                    $arrDataChartTemp[$key]['credit'] = $prevCredit;
                    $arrDataChartTemp[$key]['debit'] = $prevDebit;
                    $arrDataChartTemp[$key]['balance'] = $prevBalance;
                }
            }
            // Total & Average
            $sumGridData = (new Query())->select(['SUM(value_in) AS sum_credit', 'AVG(value_in) AS avg_credit', 'SUM(value_out) AS sum_debit', 'AVG(value_out) AS avg_debit'])
                ->from('fin_total_entry_month')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
                ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
                ->createCommand()->queryOne();
            $renderData['sumGridData'] = $sumGridData;
            // data for chart
            $arrLabelChart = [];
            $arrCreditDataChart = [];
            $arrDebitDataChart = [];
            $arrBalanceDataChart = [];
            $arrCreditAliasDataChart = [];
            $arrDebitAliasDataChart = [];
            $arrBalanceAliasDataChart = [];
            foreach ($arrDataChartTemp as $labelChart=>$dataChartTemp) {
                $arrLabelChart[] = DateTimeUtils::parse($labelChart, DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
                $arrCreditDataChart[] = $dataChartTemp['credit'];
                $arrDebitDataChart[] = $dataChartTemp['debit'];
                $arrBalanceDataChart[] = $dataChartTemp['balance'];

                $arrCreditAliasDataChart[] = NumberUtils::format($dataChartTemp['credit']);
                $arrDebitAliasDataChart[] = NumberUtils::format($dataChartTemp['debit']);
                $arrBalanceAliasDataChart[] = NumberUtils::format($dataChartTemp['balance']);
            }
            $renderData['chartData'] = json_encode(['label'=>$arrLabelChart, 'credit'=>$arrCreditDataChart, 'creditAlias'=>$arrCreditAliasDataChart,
                'debit'=>$arrDebitDataChart, 'debitAlias'=>$arrDebitAliasDataChart,
                'balance'=>$arrBalanceDataChart, 'balanceAlias'=>$arrBalanceAliasDataChart], JSON_NUMERIC_CHECK);
        }*/

        $renderData['gridData'] = $gridData;
        return $this->render('deposit', $renderData);
    }

    public function actionAssets() {
        $model = new FinTotalInterestMonth();
        $fmKeyPhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null, DateTimeUtils::FM_KEY_FMONTH);
        $fmKeyJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null, DateTimeUtils::FM_KEY_FMONTH);

        // is get page than reset value
        if (Yii::$app->request->getIsGet()) {
            $td = DateTimeUtils::getNow();
            $tdTimestamp = $td->getTimestamp();
            $tdInfo = getdate($tdTimestamp);
            $thismonth = $td->format($fmKeyPhp);

            // for report Model
            $model->fmonth = $thismonth;

            // for search Model
            $model->fmonth_to = $thismonth;
            $model->fmonth_from = DateTimeUtils::parse(($tdInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR] - 1) . '0101', DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
        } else {
            // submit data
            $postData = Yii::$app->request->post();
            $model->load($postData);

            $submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
            switch ($submitMode) {
                case MasterValueUtils::SM_MODE_INPUT:
                    /*$reportMonthInfo = getdate(DateTimeUtils::parse($model->fmonth, $fmKeyPhp)->getTimestamp());
                    $year = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
                    $month = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
                    $reportModel = FinTotalEntryMonth::findOne(['year'=>$year, 'month'=>$month, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    if (is_null($reportModel)) {
                        $reportModel = new FinTotalEntryMonth();
                        $reportModel->year = $year;
                        $reportModel->month = $month;
                    }

                    $reportMonth = DateTimeUtils::parse($year . str_pad($month, 2, '0', STR_PAD_LEFT) . '01', DateTimeUtils::FM_DEV_DATE);
                    $fromDate = $reportMonth->format(DateTimeUtils::FM_DB_DATE);
                    $toDate = DateTimeUtils::addDateTime($reportMonth, 'P1M', DateTimeUtils::FM_DB_DATE);

                    $sumEntryQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
                    $sumEntryQuery->from('fin_account_entry')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    $sumEntryQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_FIN_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_FIN_ACCOUNT_NONE]]);
                    $sumEntryQuery->andWhere(['>=', 'entry_date', $fromDate]);
                    $sumEntryQuery->andWhere(['<', 'entry_date', $toDate]);

                    $sumEntryValue = $sumEntryQuery->createCommand()->queryOne();
                    $reportModel->value_out = is_null($sumEntryValue['debit']) ? 0 : $sumEntryValue['debit'];
                    $reportModel->value_in = is_null($sumEntryValue['credit']) ? 0 : $sumEntryValue['credit'];

                    $reportModel->save();

                    Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', 'Monthly Payment Report of {month} has been saved successfully.', ['month'=>$model->fmonth]));
                    return Yii::$app->getResponse()->redirect(Url::to(['payment']));
                    */
                    break;
                default:
                    break;
            }
        }
        $renderData = ['fmKeyJui'=>$fmKeyJui, 'fmKeyPhp'=>$fmKeyPhp, 'model'=>$model];

        $fMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_from, $fmKeyPhp)->getTimestamp());
        $tMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_to, $fmKeyPhp)->getTimestamp());
        $fYear = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $fMonth = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $fMonthMM = str_pad($fMonth, 2, '0', STR_PAD_LEFT);
        $tYear = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $tMonth = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $tMonthMM = str_pad($tMonth, 2, '0', STR_PAD_LEFT);

        $gridData = null;
        /*$resultModel = FinTotalEntryMonth::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
            ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
            ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
            ->orderBy('year, month')->all();
        if (count($resultModel) > 0) {
            // Init data for chart
            $sMonth = $fYear . $fMonthMM . '01';
            $eMonth = $tYear . $tMonthMM . '01';
            $currentMonthObj = DateTimeUtils::parse($sMonth, DateTimeUtils::FM_DEV_DATE);
            $arrDataChartTemp = [];
            while ($sMonth < $eMonth) {
                $sMonth = $currentMonthObj->format(DateTimeUtils::FM_DEV_DATE);
                $arrDataChartTemp[$sMonth] = ['credit'=>0, 'debit'=>0, 'balance'=>0];

                DateTimeUtils::addDateTime($currentMonthObj, 'P1M', null, false);
            }

            $firstResult = $resultModel[0];
            $prevCredit = $firstResult->value_in;
            $prevDebit = $firstResult->value_out;
            $prevBalance = $prevCredit - $prevDebit;

            $gridData = [];
            foreach ($resultModel as $rm) {
                $key = $rm->year . str_pad($rm->month, 2, '0', STR_PAD_LEFT) . '01';
                $balance = $rm->value_in - $rm->value_out;
                $compareCredit = $rm->value_in - $prevCredit;
                $compareDebit = $rm->value_out - $prevDebit;
                $compareBalance = $balance - $prevBalance;

                $prevCredit = $rm->value_in;
                $prevDebit = $rm->value_out;
                $prevBalance = $prevCredit - $prevDebit;

                $girdRow = ['month'=>DateTimeUtils::parse($key, DateTimeUtils::FM_DEV_DATE),
                    'credit'=>$prevCredit, 'debit'=>$prevDebit, 'balance'=>$prevBalance,
                    'compareCredit'=>$compareCredit, 'compareDebit'=>$compareDebit, 'compareBalance'=>$compareBalance];
                $gridData[$key] = $girdRow;

                // data for chart
                if (isset($arrDataChartTemp[$key])) {
                    $arrDataChartTemp[$key]['credit'] = $prevCredit;
                    $arrDataChartTemp[$key]['debit'] = $prevDebit;
                    $arrDataChartTemp[$key]['balance'] = $prevBalance;
                }
            }
            // Total & Average
            $sumGridData = (new Query())->select(['SUM(value_in) AS sum_credit', 'AVG(value_in) AS avg_credit', 'SUM(value_out) AS sum_debit', 'AVG(value_out) AS avg_debit'])
                ->from('fin_total_entry_month')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
                ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
                ->createCommand()->queryOne();
            $renderData['sumGridData'] = $sumGridData;
            // data for chart
            $arrLabelChart = [];
            $arrCreditDataChart = [];
            $arrDebitDataChart = [];
            $arrBalanceDataChart = [];
            $arrCreditAliasDataChart = [];
            $arrDebitAliasDataChart = [];
            $arrBalanceAliasDataChart = [];
            foreach ($arrDataChartTemp as $labelChart=>$dataChartTemp) {
                $arrLabelChart[] = DateTimeUtils::parse($labelChart, DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
                $arrCreditDataChart[] = $dataChartTemp['credit'];
                $arrDebitDataChart[] = $dataChartTemp['debit'];
                $arrBalanceDataChart[] = $dataChartTemp['balance'];

                $arrCreditAliasDataChart[] = NumberUtils::format($dataChartTemp['credit']);
                $arrDebitAliasDataChart[] = NumberUtils::format($dataChartTemp['debit']);
                $arrBalanceAliasDataChart[] = NumberUtils::format($dataChartTemp['balance']);
            }
            $renderData['chartData'] = json_encode(['label'=>$arrLabelChart, 'credit'=>$arrCreditDataChart, 'creditAlias'=>$arrCreditAliasDataChart,
                'debit'=>$arrDebitDataChart, 'debitAlias'=>$arrDebitAliasDataChart,
                'balance'=>$arrBalanceDataChart, 'balanceAlias'=>$arrBalanceAliasDataChart], JSON_NUMERIC_CHECK);
        }*/

        $renderData['gridData'] = $gridData;
        return $this->render('assets', $renderData);
    }
}
?>