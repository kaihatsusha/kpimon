<?php
namespace app\modules\fin\controllers;

use yii\web\Controller;
use app\components\DateTimeUtils;
use app\models\FinAccount;
use app\models\extended\FinAccount01I00;
use app\models\extended\FinAccount02I00;
use app\models\extended\FinAccount03I00;
use app\models\extended\FinAccount04I00;
use app\models\extended\FinAccount05I00;
use app\models\extended\FinAccount06I00;

class AccountController extends Controller {
	public function actionIndex() {
		$arrDeposits = [];
		$sumDeposits = ['opening_balance'=>0, 'closing_interest_unit'=>0, 'closing_interest'=>0, 'closing_balance'=>0,
			'now_interest_unit'=>0, 'now_interest'=>0, 'capital'=>0, 'result_interest'=>0];
		$minClosingTimestamp = null;
		
		$arrTmAtm = [];
		$arrCredit = [];
		$arrLunchFound = [];
		$arrOtherFound = [];
		$sumTmAtm = ['opening_balance'=>0, 'closing_balance'=>0, 'now_balance'=>0];
		$sumTmAtmDeposit = ['opening_balance'=>0, 'closing_balance'=>0, 'now_balance'=>0];
		$sumTotal = ['opening_balance'=>0, 'closing_balance'=>0, 'now_balance'=>0];
		
		$arrFinAccount = FinAccount::find()->where(['delete_flag'=>0])->orderBy('order_num')->all();
		foreach ($arrFinAccount as $finAccount) {
			$instance = $finAccount->instance();
			if (($instance instanceof FinAccount01I00) || $instance instanceof FinAccount02I00) {
				// add instance TM or ATM
				$arrTmAtm[] = $instance;
				
				// sum TM - ATM
				$sumTmAtm['opening_balance'] += $instance->opening_balance;
				$sumTmAtm['closing_balance'] += $instance->closing_balance;
				$sumTmAtm['now_balance'] += $instance->now_balance;
				
				// sum TM - ATM - Deposits
				$sumTmAtmDeposit['opening_balance'] += $instance->opening_balance;
				$sumTmAtmDeposit['closing_balance'] += $instance->closing_balance;
				$sumTmAtmDeposit['now_balance'] += $instance->now_balance;
				
				// sum Total
				$sumTotal['opening_balance'] += $instance->opening_balance;
				$sumTotal['closing_balance'] += $instance->closing_balance;
				$sumTotal['now_balance'] += $instance->now_balance;
			} elseif ($instance instanceof FinAccount03I00) {
				// add instance CREDIT
				$arrCredit[] = $instance;
				
				// sum Total
				$sumTotal['opening_balance'] += $instance->opening_balance;
				$sumTotal['closing_balance'] += $instance->closing_balance;
				$sumTotal['now_balance'] += $instance->now_balance;
			} elseif ($instance instanceof FinAccount04I00) {
				// add instance Deposits
				$arrDeposits[] = $instance;
				
				// sum deposits
				$sumDeposits['opening_balance'] += $instance->opening_balance;
				$sumDeposits['closing_interest_unit'] += $instance->closing_interest_unit;
				$sumDeposits['closing_interest'] += $instance->closing_interest;
				$sumDeposits['closing_balance'] += $instance->closing_balance;
				$sumDeposits['now_interest_unit'] += $instance->now_interest_unit;
				$sumDeposits['now_interest'] += $instance->now_interest;
				$sumDeposits['capital'] += $instance->capital;
				$sumDeposits['result_interest'] += $instance->result_interest;
				
				// sum TM - ATM - Deposits
				$nowbalance = $instance->opening_balance + $instance->now_interest;
				$sumTmAtmDeposit['opening_balance'] += $instance->opening_balance;
				$sumTmAtmDeposit['closing_balance'] += $instance->closing_balance;
				$sumTmAtmDeposit['now_balance'] += $nowbalance;
				
				// sum Total
				$sumTotal['opening_balance'] += $instance->opening_balance;
				$sumTotal['closing_balance'] += $instance->closing_balance;
				$sumTotal['now_balance'] += $nowbalance;
				
				// next closing
				$timestamp = DateTimeUtils::getDateTimeFromDB($instance->closing_date)->getTimestamp();
				if (is_null($minClosingTimestamp) || ($minClosingTimestamp > $timestamp)) {
					$minClosingTimestamp = $timestamp;
				}
			} elseif ($instance instanceof FinAccount05I00) {
				// add instance LUNCH FOUND
				$arrLunchFound[] = $instance;
				
				// sum Total
				$sumTotal['opening_balance'] += $instance->opening_balance;
				$sumTotal['closing_balance'] += $instance->closing_balance;
				$sumTotal['now_balance'] += $instance->now_balance;
			} elseif ($instance instanceof FinAccount06I00) {
				// add instance OTHER FOUND
				$arrOtherFound[] = $instance;
				
				// sum Total
				$sumTotal['opening_balance'] += $instance->opening_balance;
				$sumTotal['closing_balance'] += $instance->closing_balance;
				$sumTotal['now_balance'] += $instance->now_balance;
			}
		}
		
		return $this->render('index', ['arrDeposits'=>$arrDeposits, 'sumDeposits'=>$sumDeposits, 'minClosingTimestamp'=>$minClosingTimestamp,
			'arrTmAtm'=>$arrTmAtm, 'sumTmAtm'=>$sumTmAtm, 'sumTmAtmDeposit'=>$sumTmAtmDeposit,
			'arrCredit'=>$arrCredit, 'arrLunchFound'=>$arrLunchFound, 'arrOtherFound'=>$arrOtherFound, 'sumTotal'=>$sumTotal]);
	}
}
?>