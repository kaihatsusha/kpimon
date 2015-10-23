<?php
namespace app\modules\fin\controllers;

use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\FinAccount;

class DepositController extends MobiledetectController {
	public $defaultAction = 'index';
	
	public function behaviors() {
		return [
			'access' => [
				'class' => \yii\filters\AccessControl::className(),
				'only' => ['index', 'view', 'update'],
				'rules' => [
					[
						'allow' => true, 'roles' => ['@']
					]
				]
			]
		];
    }
	
	public function actionIndex() {
		$arrDeposits = [];
		$sumDeposits = ['opening_balance'=>0, 'closing_interest_unit'=>0, 'closing_interest'=>0, 'closing_balance'=>0,
			'now_interest_unit'=>0, 'now_interest'=>0, 'capital'=>0, 'result_interest'=>0];
		$minClosingTimestamp = null;
		
		$arrFinAccount = FinAccount::find()->where(['delete_flag'=>0, 'account_type'=>MasterValueUtils::MV_FIN_ACCOUNT_TYPE_TIME_DEPOSIT])->orderBy('order_num')->all();
		foreach ($arrFinAccount as $finAccount) {
			$instance = $finAccount->instance();
			$instance->initialize();
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
			
			// next closing
			$timestamp = DateTimeUtils::getDateTimeFromDB($instance->closing_date)->getTimestamp();
			if (is_null($minClosingTimestamp) || ($minClosingTimestamp > $timestamp)) {
				$minClosingTimestamp = $timestamp;
			}
		}
		
		return $this->render('index', ['arrDeposits'=>$arrDeposits, 'sumDeposits'=>$sumDeposits, 'minClosingTimestamp'=>$minClosingTimestamp]);
	}
}
?>