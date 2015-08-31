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
		$sumDeposits = ['opening_balance'=>0, 'closing_interest_unit'=>0, 'closing_interest'=>0, 'closing_balance'=>0, 'now_interest_unit'=>0, 'now_interest'=>0];
		$minClosingTimestamp = null;
		
		$arrFinAccount = FinAccount::find()->where(['delete_flag'=>0])->orderBy('order_num')->all();
		foreach ($arrFinAccount as $finAccount) {
			$instance = $finAccount->instance();
			if ($instance instanceof FinAccount01I00) {
				
			} elseif ($instance instanceof FinAccount02I00) {
				
			} elseif ($instance instanceof FinAccount03I00) {
				
			} elseif ($instance instanceof FinAccount04I00) {
				$arrDeposits[] = $instance;
				$sumDeposits['opening_balance'] += $instance->opening_balance;
				$sumDeposits['closing_interest_unit'] += $instance->closing_interest_unit;
				$sumDeposits['closing_interest'] += $instance->closing_interest;
				$sumDeposits['closing_balance'] += $instance->closing_balance;
				$sumDeposits['now_interest_unit'] += $instance->now_interest_unit;
				$sumDeposits['now_interest'] += $instance->now_interest;
				
				// next closing
				$timestamp = DateTimeUtils::getDateTimeFromDB($instance->closing_date)->getTimestamp();
				if (is_null($minClosingTimestamp) || ($minClosingTimestamp > $timestamp)) {
					$minClosingTimestamp = $timestamp;
				}
			} elseif ($instance instanceof FinAccount05I00) {
				
			} elseif ($instance instanceof FinAccount06I00) {
				
			}
		}
		
		return $this->render('index', ['arrDeposits'=>$arrDeposits, 'sumDeposits'=>$sumDeposits, 'minClosingTimestamp'=>$minClosingTimestamp]);
	}
}
?>