<?php
namespace app\modules\fin\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\ModelUtils;
use app\controllers\MobiledetectController;
use app\models\FinAccount;
use app\models\FinAccountEntry;
use app\models\FinTimeDepositTran;

class DepositController extends MobiledetectController {
	public $objectId = false;
	public $defaultAction = 'index';
	
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

	public function actionView($id) {
		$this->objectId = $id;
		var_dump([$id]);
	}

	public function actionCreate() {
		$model = new FinTimeDepositTran();
		$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
		$arrTimedepositTrantype = MasterValueUtils::getArrData('fin_timedeposit_trantype');
		$arrSavingAccount = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
			->where(['delete_flag'=>0, 'account_type'=>4])
			->orderBy('account_type, order_num'), 'account_id', 'account_name');
		$arrCurrentAssets = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
			->where(['delete_flag'=>0, 'account_type'=>[1,2]])
			->orderBy('account_type, order_num'), 'account_id', 'account_name');

		// submit data
		$postData = Yii::$app->request->post();
		$submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;

		// populate model attributes with user inputs
		$model->load($postData);

		// init value
		FinTimeDepositTran::$_PHP_FM_SHORTDATE = $phpFmShortDate;
		$model->scenario = FinTimeDepositTran::SCENARIO_CREATE;
		if (empty($model->opening_date)) {
			$today = new \DateTime();
			$model->opening_date = $today->format($phpFmShortDate);
			DateTimeUtils::addDateTime($today, 'P1M', null, false);
			$model->closing_date = $today->format($phpFmShortDate);
		}
		if (empty($model->add_flag)) {
			$model->add_flag = MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING;
		}

		// render GUI
		$renderView = 'create';
		$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrSavingAccount'=>$arrSavingAccount, 'arrCurrentAssets'=>$arrCurrentAssets, 'arrTimedepositTrantype'=>$arrTimedepositTrantype];
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
					$result = $this->createFixedDeposit($model);
					if ($result === true) {
						Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Fixed Deposit')]));
						return Yii::$app->getResponse()->redirect(Url::to(['index']));
					} else {
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
	 * create a Fixed Deposit
	 * @param type $fixedDepositModel
	 * @throws Exception
	 * @return string|true
	 */
	private function createFixedDeposit($fixedDepositModel) {
		$transaction = Yii::$app->db->beginTransaction();
		$save = true;
		$message = null;

		// begin transaction
		try {
			// saving account
			$savingAccount = FinAccount::findOne($fixedDepositModel->saving_account);
			$savingAccount->opening_date = $fixedDepositModel->opening_date . ' 00:00:00';
			$savingAccount->closing_date = $fixedDepositModel->closing_date . ' 00:00:00';
			$savingAccount->term_interest_rate = $fixedDepositModel->interest_rate;
			// TM or ATM
			$currentAssets = FinAccount::findOne($fixedDepositModel->current_assets);
			// histort entry (interest)
			$interestEntry = new FinAccountEntry();
			$interestEntry->entry_date = $fixedDepositModel->opening_date;
			$interestEntry->entry_value = $fixedDepositModel->interest_add;
			$interestEntry->entry_status = MasterValueUtils::MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT;
			$interestEntry->description = serialize([MasterValueUtils::MV_FIN_ENTRY_LOG_INTEREST]);
			$interestEntry->account_source = 0;
			$interestEntry->account_target = $fixedDepositModel->saving_account;
			// histort entry (capital)
			$capitalEntry = new FinAccountEntry();
			$capitalEntry->entry_date = $fixedDepositModel->opening_date;
			$capitalEntry->entry_value = $fixedDepositModel->entry_value;
			$capitalEntry->entry_status = MasterValueUtils::MV_FIN_ENTRY_TYPE_DEPOSIT;
			$capitalEntry->description = serialize([MasterValueUtils::MV_FIN_ENTRY_LOG_SAVING]);
			if ($fixedDepositModel->add_flag == MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING) {
				$savingAccount->opening_balance += $fixedDepositModel->interest_add + $fixedDepositModel->entry_value;
				$savingAccount->capital += $fixedDepositModel->entry_value;

				$currentAssets->opening_balance -= $fixedDepositModel->entry_value;

				$capitalEntry->account_source = $fixedDepositModel->current_assets;
				$capitalEntry->account_target = $fixedDepositModel->saving_account;
			} else {
				$savingAccount->opening_balance += $fixedDepositModel->interest_add - $fixedDepositModel->entry_value;
				$savingAccount->capital -= $fixedDepositModel->entry_value;

				$currentAssets->opening_balance += $fixedDepositModel->entry_value;

				$capitalEntry->account_source = $fixedDepositModel->saving_account;
				$capitalEntry->account_target = $fixedDepositModel->current_assets;
			}

			// interest unit
			$instance = $savingAccount->instance();
			$instance->initialize();
			$fixedDepositModel->interest_unit = $instance->closing_interest_unit;

			$save = $savingAccount->save();
			if ($save !== false) {
				$save = $currentAssets->save();
			}
			if ($save !== false) {
				$save = $interestEntry->save();
			}
			if ($save !== false) {
				$save = $capitalEntry->save();
			}
			if ($save !== false) {
				$save = $fixedDepositModel->save();
			}
		} catch(Exception $e) {
			$save = false;
			$message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Fixed Deposit')]);
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
	}

	public function actionCopy($id) {
		$this->objectId = $id;
	}
}
?>