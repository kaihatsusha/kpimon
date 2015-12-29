<?php
namespace app\modules\fin\controllers;

use app\components\NumberUtils;
use app\models\FinTotalAssetsMonth;
use Yii;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\FinAccount;
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
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $startDateJui = DateTimeUtils::parse('20151001', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        $fmKeyPhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null, DateTimeUtils::FM_KEY_FMONTH);
        $fmKeyJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null, DateTimeUtils::FM_KEY_FMONTH);
        $td = DateTimeUtils::getNow();

        // is get page than reset value
        if (Yii::$app->request->getIsGet()) {
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
                    $reportMonthStr = DateTimeUtils::parse($model->fmonth, $fmKeyPhp, DateTimeUtils::FM_DEV_YM) . '01';
                    $reportMonthObj = DateTimeUtils::parse($reportMonthStr, DateTimeUtils::FM_DEV_DATE);
                    $reportMonthInfo = getdate($reportMonthObj->getTimestamp());
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
        $renderData = ['fmKeyJui'=>$fmKeyJui, 'fmKeyPhp'=>$fmKeyPhp, 'model'=>$model, 'startDateJui'=>$startDateJui];

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
            while ($sMonth <= $eMonth) {
                $arrDataChartTemp[$sMonth] = ['credit'=>0, 'debit'=>0, 'balance'=>0];

                DateTimeUtils::addDateTime($currentMonthObj, 'P1M', null, false);
                $sMonth = $currentMonthObj->format(DateTimeUtils::FM_DEV_DATE);
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

        // sum payment current month
        $beginCurrentMonth = DateTimeUtils::parse($td->format(DateTimeUtils::FM_DEV_YM) . '01', DateTimeUtils::FM_DEV_DATE);
        $endCurrentMonth = DateTimeUtils::addDateTime($beginCurrentMonth, 'P1M');
        DateTimeUtils::subDateTime($endCurrentMonth, 'P1D', null, false);
        $sumCurrentMonthQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
        $sumCurrentMonthQuery->from('fin_account_entry')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
        $sumCurrentMonthQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_FIN_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_FIN_ACCOUNT_NONE]]);
        $sumCurrentMonthQuery->andWhere(['>=', 'entry_date', $beginCurrentMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentMonthQuery->andWhere(['<=', 'entry_date', $endCurrentMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentMonthData = $sumCurrentMonthQuery->createCommand()->queryOne();
        $renderData['sumCurrentMonthData'] = $sumCurrentMonthData;

        return $this->render('payment', $renderData);
    }

    public function actionDeposit() {
        $model = new FinTotalInterestMonth();
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $startDateJui = DateTimeUtils::parse('20151001', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        $fmKeyPhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null, DateTimeUtils::FM_KEY_FMONTH);
        $fmKeyJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null, DateTimeUtils::FM_KEY_FMONTH);
        $td = DateTimeUtils::getNow();

        // is get page than reset value
        if (Yii::$app->request->getIsGet()) {
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
                    $reportMonthStr = DateTimeUtils::parse($model->fmonth, $fmKeyPhp, DateTimeUtils::FM_DEV_YM) . '01';
                    $reportMonthObj = DateTimeUtils::parse($reportMonthStr, DateTimeUtils::FM_DEV_DATE);
                    $reportMonthInfo = getdate($reportMonthObj->getTimestamp());
                    $year = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
                    $month = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
                    $reportModel = FinTotalInterestMonth::findOne(['year'=>$year, 'month'=>$month, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    if (is_null($reportModel)) {
                        $reportModel = new FinTotalInterestMonth();
                        $reportModel->year = $year;
                        $reportModel->month = $month;
                    }

                    $reportMonthStr = $year . str_pad($month, 2, '0', STR_PAD_LEFT);
                    $reportMonth = DateTimeUtils::parse($reportMonthStr . '01', DateTimeUtils::FM_DEV_DATE);
                    $fromDate = $reportMonth->format(DateTimeUtils::FM_DB_DATE);
                    DateTimeUtils::addDateTime($reportMonth, 'P1M', null, false);
                    $toDate = DateTimeUtils::subDateTime($reportMonth, 'P1D', DateTimeUtils::FM_DB_DATE);

                    $interestUnitQuery = (new Query())->select(['start_date', 'end_date', 'interest_unit']);
                    $interestUnitQuery->from('fin_total_interest_unit')->where(['<=', 'start_date', $toDate]);
                    $interestUnitQuery->andWhere(['OR', ['>=', 'end_date', $fromDate], ['is', 'end_date', null]]);

                    $totalInterestMonth = 0;
                    $arrInterestUnit = $interestUnitQuery->createCommand()->queryAll();
                    foreach ($arrInterestUnit as $interestUnit) {
                        $unit = $interestUnit['interest_unit'];
                        $currentDateObj = DateTimeUtils::getDateFromDB($interestUnit['start_date']);
                        $startDate = $currentDateObj->format(DateTimeUtils::FM_DEV_DATE);
                        $endDate = empty($interestUnit['end_date']) ? DateTimeUtils::formatNow(DateTimeUtils::FM_DEV_DATE) : DateTimeUtils::formatDateFromDB($interestUnit['end_date'], DateTimeUtils::FM_DEV_DATE);

                        while ($startDate <= $endDate) {
                            $currentMonthStr = $currentDateObj->format(DateTimeUtils::FM_DEV_YM);
                            if ($currentMonthStr == $reportMonthStr) {
                                $totalInterestMonth += $unit;
                            }

                            DateTimeUtils::addDateTime($currentDateObj, 'P1D', null, false);
                            $startDate = $currentDateObj->format(DateTimeUtils::FM_DEV_DATE);
                        }
                    }

                    $reportModel->term_interest = intval($totalInterestMonth);
                    $reportModel->save();

                    Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', 'Monthly Interest Report of {month} has been saved successfully.', ['month'=>$model->fmonth]));
                    return Yii::$app->getResponse()->redirect(Url::to(['deposit']));
                    break;
                default:
                    break;
            }
        }
        $renderData = ['fmKeyJui'=>$fmKeyJui, 'fmKeyPhp'=>$fmKeyPhp, 'model'=>$model, 'startDateJui'=>$startDateJui];

        $fMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_from, $fmKeyPhp)->getTimestamp());
        $tMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_to, $fmKeyPhp)->getTimestamp());
        $fYear = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $fMonth = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $fMonthMM = str_pad($fMonth, 2, '0', STR_PAD_LEFT);
        $tYear = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $tMonth = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $tMonthMM = str_pad($tMonth, 2, '0', STR_PAD_LEFT);

        $gridData = null;
        $resultModel = FinTotalInterestMonth::find()->where(['delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
            ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
            ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
            ->orderBy('year, month')->all();
        if (count($resultModel) > 0) {
            // Init data for chart
            $sMonth = $fYear . $fMonthMM . '01';
            $eMonth = $tYear . $tMonthMM . '01';
            $currentMonthObj = DateTimeUtils::parse($sMonth, DateTimeUtils::FM_DEV_DATE);
            $arrDataChartTemp = [];
            while ($sMonth <= $eMonth) {
                $arrDataChartTemp[$sMonth] = ['noterm'=>0, 'term'=>0, 'total'=>0];

                DateTimeUtils::addDateTime($currentMonthObj, 'P1M', null, false);
                $sMonth = $currentMonthObj->format(DateTimeUtils::FM_DEV_DATE);
            }

            $firstResult = $resultModel[0];
            $prevNoterm = $firstResult->noterm_interest;
            $prevTerm = $firstResult->term_interest;
            $prevTotal = $prevNoterm + $prevTerm;

            $gridData = [];
            foreach ($resultModel as $rm) {
                $key = $rm->year . str_pad($rm->month, 2, '0', STR_PAD_LEFT) . '01';
                $total = $rm->noterm_interest + $rm->term_interest;
                $compareNoterm = $rm->noterm_interest - $prevNoterm;
                $compareTerm = $rm->term_interest - $prevTerm;
                $compareTotal = $total - $prevTotal;

                $prevNoterm = $rm->noterm_interest;
                $prevTerm = $rm->term_interest;
                $prevTotal = $prevNoterm + $prevTerm;

                $girdRow = ['month'=>DateTimeUtils::parse($key, DateTimeUtils::FM_DEV_DATE),
                    'noterm'=>$prevNoterm, 'term'=>$prevTerm, 'total'=>$prevTotal,
                    'compareNoterm'=>$compareNoterm, 'compareTerm'=>$compareTerm, 'compareTotal'=>$compareTotal];
                $gridData[$key] = $girdRow;

                // data for chart
                if (isset($arrDataChartTemp[$key])) {
                    $arrDataChartTemp[$key]['noterm'] = $prevNoterm;
                    $arrDataChartTemp[$key]['term'] = $prevTerm;
                    $arrDataChartTemp[$key]['total'] = $prevTotal;
                }
            }
            // Total & Average
            $sumGridData = (new Query())->select(['SUM(noterm_interest) AS sum_noterm', 'AVG(noterm_interest) AS avg_noterm', 'SUM(term_interest) AS sum_term', 'AVG(term_interest) AS avg_term'])
                ->from('fin_total_interest_month')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
                ->andWhere(['>=', 'year', $fYear])->andWhere(['>=', 'month', $fMonth])
                ->andWhere(['<=', 'year', $tYear])->andWhere(['<=', 'month', $tMonth])
                ->createCommand()->queryOne();
            $renderData['sumGridData'] = $sumGridData;
            // data for chart
            $arrLabelChart = [];
            $arrNotermDataChart = [];
            $arrTermDataChart = [];
            $arrTotalDataChart = [];
            $arrNotermAliasDataChart = [];
            $arrTermAliasDataChart = [];
            $arrTotalAliasDataChart = [];
            foreach ($arrDataChartTemp as $labelChart=>$dataChartTemp) {
                $arrLabelChart[] = DateTimeUtils::parse($labelChart, DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
                $arrNotermDataChart[] = $dataChartTemp['noterm'];
                $arrTermDataChart[] = $dataChartTemp['term'];
                $arrTotalDataChart[] = $dataChartTemp['total'];

                $arrNotermAliasDataChart[] = NumberUtils::format($dataChartTemp['noterm']);
                $arrTermAliasDataChart[] = NumberUtils::format($dataChartTemp['term']);
                $arrTotalAliasDataChart[] = NumberUtils::format($dataChartTemp['total']);
            }
            $renderData['chartData'] = json_encode(['label'=>$arrLabelChart, 'noterm'=>$arrNotermDataChart, 'notermAlias'=>$arrNotermAliasDataChart,
                'term'=>$arrTermDataChart, 'termAlias'=>$arrTermAliasDataChart,
                'total'=>$arrTotalDataChart, 'totalAlias'=>$arrTotalAliasDataChart], JSON_NUMERIC_CHECK);
        }
        $renderData['gridData'] = $gridData;

        // sum Term Interest current month
        $beginCurrentMonth = DateTimeUtils::parse($td->format(DateTimeUtils::FM_DEV_YM) . '01', DateTimeUtils::FM_DEV_DATE);
        $endCurrentMonth = DateTimeUtils::addDateTime($beginCurrentMonth, 'P1M');
        DateTimeUtils::subDateTime($endCurrentMonth, 'P1D', null, false);
        $currentInterestUnitQuery = (new Query())->select(['start_date', 'end_date', 'interest_unit']);
        $currentInterestUnitQuery->from('fin_total_interest_unit')->where(['<=', 'start_date', $endCurrentMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $currentInterestUnitQuery->andWhere(['OR', ['>=', 'end_date', $beginCurrentMonth->format(DateTimeUtils::FM_DB_DATE)], ['is', 'end_date', null]]);
        $currentTermInterestMonth = 0;
        $arrCurrentInterestUnit = $currentInterestUnitQuery->createCommand()->queryAll();
        $requireCurrentMonthStr = $beginCurrentMonth->format(DateTimeUtils::FM_DEV_YM);
        foreach ($arrCurrentInterestUnit as $currentInterestUnit) {
            $currentUnit = $currentInterestUnit['interest_unit'];
            $currentDateObj = DateTimeUtils::getDateFromDB($currentInterestUnit['start_date']);
            $currentStartDate = $currentDateObj->format(DateTimeUtils::FM_DEV_DATE);
            $currentEndDate = empty($currentInterestUnit['end_date']) ? DateTimeUtils::formatNow(DateTimeUtils::FM_DEV_DATE) : DateTimeUtils::formatDateFromDB($currentInterestUnit['end_date'], DateTimeUtils::FM_DEV_DATE);
            while ($currentStartDate <= $currentEndDate) {
                $currentMonthStr = $currentDateObj->format(DateTimeUtils::FM_DEV_YM);
                if ($currentMonthStr == $requireCurrentMonthStr) {
                    $currentTermInterestMonth += $currentUnit;
                }

                DateTimeUtils::addDateTime($currentDateObj, 'P1D', null, false);
                $currentStartDate = $currentDateObj->format(DateTimeUtils::FM_DEV_DATE);
            }
        }
        // sum No Term Interest current month
        $currentNotermInterestMonth = 0;
        $arrFinAccount = FinAccount::find()->where(['delete_flag'=>0, 'account_type'=>MasterValueUtils::MV_FIN_ACCOUNT_TYPE_CURRENT])->all();
        foreach ($arrFinAccount as $finAccount) {
            $instance = $finAccount->instance();
            $instance->initialize();
            $currentNotermInterestMonth += $instance->capital + $instance->now_interest;
        }
        $renderData['sumCurrentInterestData'] = ['term'=>$currentTermInterestMonth, 'noterm'=>$currentNotermInterestMonth];

        return $this->render('deposit', $renderData);
    }

    public function actionAssets() {
        $model = new FinTotalAssetsMonth();
        $fmShortDatePhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null);
        $startDateJui = DateTimeUtils::parse('20151101', DateTimeUtils::FM_DEV_DATE, $fmShortDatePhp);
        $fmKeyPhp = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_PHP, null, DateTimeUtils::FM_KEY_FMONTH);
        $fmKeyJui = DateTimeUtils::getDateFormat(DateTimeUtils::FM_KEY_JUI, null, DateTimeUtils::FM_KEY_FMONTH);
        $td = DateTimeUtils::getNow();

        // is get page than reset value
        if (Yii::$app->request->getIsGet()) {
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
                    $reportMonthStr = DateTimeUtils::parse($model->fmonth, $fmKeyPhp, DateTimeUtils::FM_DEV_YM) . '01';
                    $reportMonthObj = DateTimeUtils::parse($reportMonthStr, DateTimeUtils::FM_DEV_DATE);
                    $reportMonthInfo = getdate($reportMonthObj->getTimestamp());
                    $year = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
                    $month = $reportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
                    $reportModel = FinTotalAssetsMonth::findOne(['year'=>$year, 'month'=>$month, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    if (is_null($reportModel)) {
                        $reportModel = new FinTotalAssetsMonth();
                        $reportModel->year = $year;
                        $reportModel->month = $month;
                    }

                    $prevReportMonthObj = DateTimeUtils::subDateTime($reportMonthObj, 'P1M');
                    $prevReportMonthInfo = getdate($prevReportMonthObj->getTimestamp());
                    $prevYear = $prevReportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
                    $prevMonth = $prevReportMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
                    $prevReportModel = FinTotalAssetsMonth::findOne(['year'=>$prevYear, 'month'=>$prevMonth, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    $prevAssetsValue = is_null($prevReportModel) ? 0 : $prevReportModel->assets_value;

                    $totalEntryMonth = FinTotalEntryMonth::findOne(['year'=>$year, 'month'=>$month, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
                    $sumdebit = 0;
                    $sumcredit = 0;
                    if (!is_null($totalEntryMonth)) {
                        $sumdebit = is_null($totalEntryMonth->value_out) ? 0 : $totalEntryMonth->value_out;
                        $sumcredit = is_null($totalEntryMonth->value_in) ? 0 : $totalEntryMonth->value_in;
                    }
                    $reportModel->assets_value = $prevAssetsValue + $sumcredit - $sumdebit;
                    $reportModel->save();

                    Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', 'Monthly Assets Report of {month} has been saved successfully.', ['month'=>$model->fmonth]));
                    return Yii::$app->getResponse()->redirect(Url::to(['assets']));
                    break;
                default:
                    break;
            }
        }
        $renderData = ['fmKeyJui'=>$fmKeyJui, 'fmKeyPhp'=>$fmKeyPhp, 'model'=>$model, 'startDateJui'=>$startDateJui];

        $fMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_from, $fmKeyPhp)->getTimestamp());
        $tMonthInfo = getdate(DateTimeUtils::parse($model->fmonth_to, $fmKeyPhp)->getTimestamp());
        $fYear = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $fMonth = $fMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $fMonthMM = str_pad($fMonth, 2, '0', STR_PAD_LEFT);
        $tYear = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_YEAR];
        $tMonth = $tMonthInfo[DateTimeUtils::FN_KEY_GETDATE_MONTH_INT];
        $tMonthMM = str_pad($tMonth, 2, '0', STR_PAD_LEFT);

        $gridData = null;
        $resultModel = FinTotalAssetsMonth::find()->select('t1.*, t2.value_in AS credit, t2.value_out AS debit')->from('fin_total_assets_month t1')
            ->leftJoin('fin_total_entry_month t2', '(t1.year = t2.year AND t1.month = t2.month)')->where(['t1.delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE])
            ->andWhere(['>=', 't1.year', $fYear])->andWhere(['>=', 't1.month', $fMonth])
            ->andWhere(['<=', 't1.year', $tYear])->andWhere(['<=', 't1.month', $tMonth])
            ->orderBy('t1.year, t1.month')->all();
        if (count($resultModel) > 0) {
            // Init data for chart
            $sMonth = $fYear . $fMonthMM . '01';
            $eMonth = $tYear . $tMonthMM . '01';
            $currentMonthObj = DateTimeUtils::parse($sMonth, DateTimeUtils::FM_DEV_DATE);
            $arrDataChartTemp = [];
            while ($sMonth < $eMonth) {
                $sMonth = $currentMonthObj->format(DateTimeUtils::FM_DEV_DATE);
                $arrDataChartTemp[$sMonth] = ['credit'=>0, 'debit'=>0, 'assets'=>0];

                DateTimeUtils::addDateTime($currentMonthObj, 'P1M', null, false);
            }

            $firstResult = $resultModel[0];
            $prevCredit = is_null($firstResult->credit) ? 0 : $firstResult->credit;
            $prevDebit = is_null($firstResult->debit) ? 0 : $firstResult->debit;
            $prevAssets = is_null($firstResult->assets_value) ? 0 : $firstResult->assets_value;

            $gridData = [];
            foreach ($resultModel as $rm) {
                $key = $rm->year . str_pad($rm->month, 2, '0', STR_PAD_LEFT) . '01';
                $tempCredit = is_null($rm->credit) ? 0 : $rm->credit;
                $tempDebit = is_null($rm->debit) ? 0 : $rm->debit;
                $tempAssets = is_null($rm->assets_value) ? 0 : $rm->assets_value;

                $compareCredit = $tempCredit - $prevCredit;
                $compareDebit = $tempDebit - $prevDebit;
                $compareAssets = $tempAssets - $prevAssets;

                $prevCredit = $tempCredit;
                $prevDebit = $tempDebit;
                $prevAssets = $tempAssets;

                $girdRow = ['month'=>DateTimeUtils::parse($key, DateTimeUtils::FM_DEV_DATE),
                    'credit'=>$prevCredit, 'debit'=>$prevDebit, 'assets'=>$prevAssets,
                    'compareCredit'=>$compareCredit, 'compareDebit'=>$compareDebit, 'compareAssets'=>$compareAssets];
                $gridData[$key] = $girdRow;

                // data for chart
                if (isset($arrDataChartTemp[$key])) {
                    $arrDataChartTemp[$key]['credit'] = $prevCredit;
                    $arrDataChartTemp[$key]['debit'] = $prevDebit;
                    $arrDataChartTemp[$key]['assets'] = $prevAssets;
                }
            }
            // data for chart
            $arrLabelChart = [];
            $arrCreditDataChart = [];
            $arrDebitDataChart = [];
            $arrAssetsDataChart = [];
            $arrCreditAliasDataChart = [];
            $arrDebitAliasDataChart = [];
            $arrAssetsAliasDataChart = [];
            foreach ($arrDataChartTemp as $labelChart=>$dataChartTemp) {
                $arrLabelChart[] = DateTimeUtils::parse($labelChart, DateTimeUtils::FM_DEV_DATE, $fmKeyPhp);
                $arrCreditDataChart[] = $dataChartTemp['credit'];
                $arrDebitDataChart[] = $dataChartTemp['debit'];
                $arrAssetsDataChart[] = $dataChartTemp['assets'];

                $arrCreditAliasDataChart[] = NumberUtils::format($dataChartTemp['credit']);
                $arrDebitAliasDataChart[] = NumberUtils::format($dataChartTemp['debit']);
                $arrAssetsAliasDataChart[] = NumberUtils::format($dataChartTemp['assets']);
            }
            $renderData['chartData'] = json_encode(['label'=>$arrLabelChart, 'credit'=>$arrCreditDataChart, 'creditAlias'=>$arrCreditAliasDataChart,
                'debit'=>$arrDebitDataChart, 'debitAlias'=>$arrDebitAliasDataChart,
                'assets'=>$arrAssetsDataChart, 'assetsAlias'=>$arrAssetsAliasDataChart], JSON_NUMERIC_CHECK);
        }
        $renderData['gridData'] = $gridData;

        // sum payment current month
        $beginCurrentMonth = DateTimeUtils::parse($td->format(DateTimeUtils::FM_DEV_YM) . '01', DateTimeUtils::FM_DEV_DATE);
        $endCurrentMonth = DateTimeUtils::addDateTime($beginCurrentMonth, 'P1M');
        DateTimeUtils::subDateTime($endCurrentMonth, 'P1D', null, false);
        $sumCurrentMonthQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
        $sumCurrentMonthQuery->from('fin_account_entry')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
        $sumCurrentMonthQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_FIN_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_FIN_ACCOUNT_NONE]]);
        $sumCurrentMonthQuery->andWhere(['>=', 'entry_date', $beginCurrentMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentMonthQuery->andWhere(['<=', 'entry_date', $endCurrentMonth->format(DateTimeUtils::FM_DB_DATE)]);
        $sumCurrentAssetsData = $sumCurrentMonthQuery->createCommand()->queryOne();
        // sum Assets current month
        $sumCurrentAssets = 0;
        $arrFinAccount = FinAccount::find()->where(['delete_flag'=>0])->all();
        foreach ($arrFinAccount as $finAccount) {
            $instance = $finAccount->instance();
            $instance->initialize();
            $sumCurrentAssets += $instance->opening_balance;
        }
        $sumCurrentAssetsData['assets'] = $sumCurrentAssets;
        $renderData['sumCurrentAssetsData'] = $sumCurrentAssetsData;

        return $this->render('assets', $renderData);
    }
}
?>