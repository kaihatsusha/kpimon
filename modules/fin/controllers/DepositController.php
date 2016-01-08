<?php
namespace app\modules\fin\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\MasterValueUtils;
use app\components\ModelUtils;
use app\controllers\MobiledetectController;
use app\models\FinAccount;
use app\models\FinAccountEntry;
use app\models\FinTimeDepositTran;

class DepositController extends MobiledetectController {
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
		$searchModel = new FinTimeDepositTran();
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

		// populate model attributes with user inputs
		$searchModel->load($postData);

		// init value
		if (Yii::$app->request->getIsGet()) {
			$today = new \DateTime();
			$searchModel->opening_date_to = $today->format($phpFmShortDate);
			$lastMonth = DateTimeUtils::getNow(DateTimeUtils::FM_DEV_YM . '01', DateTimeUtils::FM_DEV_DATE);
			DateTimeUtils::subDateTime($lastMonth, 'P3M', null, false);
			$searchModel->opening_date_from = $lastMonth->format($phpFmShortDate);
		}
		FinTimeDepositTran::$_PHP_FM_SHORTDATE = $phpFmShortDate;
		$searchModel->scenario = MasterValueUtils::SCENARIO_LIST;

		// sum Interest & Principal Amount (Adding funds OR Partial withdrawal)
		$sumTimeDepositValue = false;
		// query for dataprovider
		$dataQuery = null;
		if ($searchModel->validate()) {
			$dataQuery = FinTimeDepositTran::find()->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
			$sumTimeDepositQuery = (new Query())->select(['SUM(interest_add) AS interest_add, SUM(IF(add_flag = 1, entry_value, 0)) AS adding_value, SUM(IF(add_flag = 2, entry_value, 0)) AS withdrawal_value']);
			$sumTimeDepositQuery->from('fin_time_deposit_tran')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
			if (!empty($searchModel->opening_date_from)) {
				$dataQuery->andWhere(['>=', 'opening_date', $searchModel->opening_date_from]);
				$sumTimeDepositQuery->andWhere(['>=', 'opening_date', $searchModel->opening_date_from]);
			}
			if (!empty($searchModel->opening_date_to)) {
				$dataQuery->andWhere(['<=', 'opening_date', $searchModel->opening_date_to]);
				$sumTimeDepositQuery->andWhere(['<=', 'opening_date', $searchModel->opening_date_to]);
			}
			if ($searchModel->saving_account > 0) {
				$dataQuery->andWhere(['=', 'saving_account', $searchModel->saving_account]);
				$sumTimeDepositQuery->andWhere(['=', 'saving_account', $searchModel->saving_account]);
			}
			if ($searchModel->current_assets > 0) {
				$dataQuery->andWhere(['=', 'current_assets', $searchModel->current_assets]);
				$sumTimeDepositQuery->andWhere(['=', 'current_assets', $searchModel->current_assets]);
			}
			$dataQuery->orderBy('opening_date DESC, create_date DESC');
			$sumTimeDepositValue = $sumTimeDepositQuery->createCommand()->queryOne();
		} else {
			$dataQuery = FinTimeDepositTran::find()->where(['transactions_id'=>-1]);
		}

		// render GUI
		$renderData = ['searchModel'=>$searchModel, 'dataQuery'=>$dataQuery, 'phpFmShortDate'=>$phpFmShortDate, 'sumTimeDepositValue'=>$sumTimeDepositValue,
			'arrCurrentAssets'=>$arrCurrentAssets, 'arrSavingAccount'=>$arrSavingAccount, 'arrTimedepositTrantype'=>$arrTimedepositTrantype];

		return $this->render('index', $renderData);
	}

	public function actionView($id) {
		$this->objectId = $id;
		$model = FinTimeDepositTran::findOne(['transactions_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

		$renderView = 'view';
		if (is_null($model)) {
			$model = false;
			$renderData = ['model'=>$model];
			Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Fixed Deposit')]));
		} else {
			// master value
			$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
			$arrTimedepositTrantype = MasterValueUtils::getArrData('fin_timedeposit_trantype');
			$arrSavingAccount = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
				->where(['delete_flag'=>0, 'account_type'=>4])
				->orderBy('account_type, order_num'), 'account_id', 'account_name');
			$arrCurrentAssets = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
				->where(['delete_flag'=>0, 'account_type'=>[1,2]])
				->orderBy('account_type, order_num'), 'account_id', 'account_name');

			// data for rendering
			$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrTimedepositTrantype'=>$arrTimedepositTrantype, 'arrSavingAccount'=>$arrSavingAccount, 'arrCurrentAssets'=>$arrCurrentAssets];
		}

		// render GUI
		return $this->render($renderView, $renderData);
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
		FinTimeDepositTran::$_ARR_SAVING_ACOUNT = $arrSavingAccount;
		$model->scenario = MasterValueUtils::SCENARIO_CREATE;
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
			// history entry (interest)
			$interestEntry = new FinAccountEntry();
			$interestEntry->entry_date = $fixedDepositModel->opening_date;
			$interestEntry->entry_value = $fixedDepositModel->interest_add;
			$interestEntry->entry_status = MasterValueUtils::MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT;
			$interestEntry->description = serialize([MasterValueUtils::MV_FIN_ENTRY_LOG_INTEREST]);
			$interestEntry->account_source = 0;
			$interestEntry->account_target = $fixedDepositModel->saving_account;
			// history entry (capital)
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
		$model = FinTimeDepositTran::findOne(['transactions_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

		$renderView = 'update';
		if (is_null($model)) {
			$model = false;
			$renderData = ['model'=>$model];
			Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Fixed Deposit')]));
		} else {
			// back up data
			$model->backup();

			// master value
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
			FinTimeDepositTran::$_ARR_SAVING_ACOUNT = $arrSavingAccount;
			$model->scenario = MasterValueUtils::SCENARIO_UPDATE;
			$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrTimedepositTrantype'=>$arrTimedepositTrantype, 'arrSavingAccount'=>$arrSavingAccount, 'arrCurrentAssets'=>$arrCurrentAssets];
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
						$result = $this->updateFixedDeposit($model);
						if ($result === true) {
							Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Fixed Deposit')]));
							return Yii::$app->getResponse()->redirect(Url::to(['update', 'id'=>$id]));
						} else {
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
	 * update a Fixed Deposit
	 * @param type $fixedDepositModel
	 * @throws Exception
	 * @return string|true
	 */
	private function updateFixedDeposit($fixedDepositModel) {
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
			// history entry (interest)
			$interestEntry = FinAccountEntry::findOne(['entry_date'=>$fixedDepositModel->opening_date,
				'entry_status'=>MasterValueUtils::MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT, 'account_source'=>0,
				'account_target'=>$fixedDepositModel->saving_account]);
			$interestEntry->entry_value = $fixedDepositModel->interest_add;
			// history entry (capital)
			$capitalCondition = ['entry_date'=>$fixedDepositModel->opening_date, 'entry_status'=>MasterValueUtils::MV_FIN_ENTRY_TYPE_DEPOSIT];
			if ($fixedDepositModel->add_flag == MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING) {
				$capitalCondition['account_source'] = $fixedDepositModel->current_assets;
				$capitalCondition['account_target'] = $fixedDepositModel->saving_account;
			} else {
				$capitalCondition['account_source'] = $fixedDepositModel->saving_account;
				$capitalCondition['account_target'] = $fixedDepositModel->current_assets;
			}
			$capitalEntry = FinAccountEntry::findOne($capitalCondition);
			$capitalEntry->entry_value = $fixedDepositModel->entry_value;

			$bkData = $fixedDepositModel->BACK_UP;
			if ($fixedDepositModel->add_flag == MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING) {
				$savingAccount->opening_balance = $savingAccount->opening_balance + $fixedDepositModel->interest_add + $fixedDepositModel->entry_value - $bkData['interest_add'] - $bkData['entry_value'];
				$savingAccount->capital = $savingAccount->capital + $fixedDepositModel->entry_value - $bkData['entry_value'];

				$currentAssets->opening_balance = $currentAssets->opening_balance - $fixedDepositModel->entry_value + $bkData['entry_value'];
			} else {
				$savingAccount->opening_balance = $savingAccount->opening_balance + $fixedDepositModel->interest_add - $fixedDepositModel->entry_value -$bkData['interest_add'] + $bkData['entry_value'];
				$savingAccount->capital = $savingAccount->capital - $fixedDepositModel->entry_value + $bkData['entry_value'];

				$currentAssets->opening_balance = $currentAssets->opening_balance + $fixedDepositModel->entry_value - $bkData['entry_value'];
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

	public function actionCopy($id) {
		$this->objectId = $id;
		$model = FinTimeDepositTran::findOne(['transactions_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);

		$renderView = 'copy';
		if (is_null($model)) {
			$model = false;
			$renderData = ['model'=>$model];
			Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Fixed Deposit')]));
		} else {
			// master value
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
			// reset value
			if (Yii::$app->request->getIsGet()) {
				$openingDate = DateTimeUtils::getDateFromDB($model->opening_date);
				$closingDate = DateTimeUtils::getDateFromDB($model->closing_date);
				$dateDiff = $closingDate->diff($openingDate);
				$interval = 'P' . $dateDiff->m . 'M';
				DateTimeUtils::addDateTime($closingDate, $interval, null, false);

				$model->opening_date = $model->closing_date;
				$model->closing_date = $closingDate->format(DateTimeUtils::FM_DB_DATE);
				$model->interest_add = 0;
				$model->add_flag = MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING;
			}
			// init value
			FinTimeDepositTran::$_PHP_FM_SHORTDATE = $phpFmShortDate;
			FinTimeDepositTran::$_ARR_SAVING_ACOUNT = $arrSavingAccount;
			$model->scenario = MasterValueUtils::SCENARIO_COPY;
			$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrTimedepositTrantype'=>$arrTimedepositTrantype, 'arrSavingAccount'=>$arrSavingAccount, 'arrCurrentAssets'=>$arrCurrentAssets];
			switch ($submitMode) {
				case MasterValueUtils::SM_MODE_INPUT:
					$isValid = $model->validate();
					if ($isValid) {
						$renderView = 'confirm';
						$renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_COPY];
					}
					break;
				case MasterValueUtils::SM_MODE_CONFIRM:
					$isValid = $model->validate();
					if ($isValid) {
						$result = $this->copyFixedDeposit($model);
						if ($result === true) {
							Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Fixed Deposit')]));
							return Yii::$app->getResponse()->redirect(Url::to(['index']));
						} else {
							Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, $result);
							$renderView = 'confirm';
							$renderData['formMode'] = [MasterValueUtils::PG_MODE_NAME=>MasterValueUtils::PG_MODE_COPY];
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
	 * copy a Fixed Deposit
	 * @param type $fixedDepositModel
	 * @throws Exception
	 * @return string|true
	 */
	private function copyFixedDeposit($fixedDepositModel) {
		$fixedDepositModel->setIsNewRecord(true);
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
			// history entry (interest)
			$interestEntry = new FinAccountEntry();
			$interestEntry->entry_date = $fixedDepositModel->opening_date;
			$interestEntry->entry_value = $fixedDepositModel->interest_add;
			$interestEntry->entry_status = MasterValueUtils::MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT;
			$interestEntry->description = serialize([MasterValueUtils::MV_FIN_ENTRY_LOG_INTEREST]);
			$interestEntry->account_source = 0;
			$interestEntry->account_target = $fixedDepositModel->saving_account;
			// history entry (capital)
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
}
?>