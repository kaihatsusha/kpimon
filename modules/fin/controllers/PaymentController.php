<?php
namespace app\modules\fin\controllers;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Url;
use app\components\DateTimeUtils;
use app\components\ModelUtils;
use app\components\MasterValueUtils;
use app\components\StringUtils;
use app\controllers\MobiledetectController;
use app\models\FinAccount;
use app\models\FinAccountEntry;
use app\models\OthNote;

class PaymentController extends MobiledetectController {
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
		$searchModel = new FinAccountEntry();
		$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
		$arrFinAccount = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
				->where(['delete_flag'=>0])
				->orderBy('account_type, order_num'), 'account_id', 'account_name');
		$arrEntryLog = MasterValueUtils::getArrData('fin_entry_log');
		
		// submit data
		$postData = Yii::$app->request->post();
		
		// populate model attributes with user inputs
		$searchModel->load($postData);
		
		// init value
		$today = new \DateTime();
		if (Yii::$app->request->getIsGet()) {
			$searchModel->entry_date_to = $today->format($phpFmShortDate);
			$lastMonth = DateTimeUtils::getNow(DateTimeUtils::FM_DEV_YM . '01', DateTimeUtils::FM_DEV_DATE);
			DateTimeUtils::subDateTime($lastMonth, 'P1M', null, false);
			$searchModel->entry_date_from = $lastMonth->format($phpFmShortDate);
		}
		FinAccountEntry::$_PHP_FM_SHORTDATE = $phpFmShortDate;
		$searchModel->scenario = MasterValueUtils::SCENARIO_LIST;

		// sum current month
		$beginMonth = DateTimeUtils::parse($today->format(DateTimeUtils::FM_DEV_YM) . '01', DateTimeUtils::FM_DEV_DATE);
		$endMonth = DateTimeUtils::addDateTime($beginMonth, 'P1M');
		DateTimeUtils::subDateTime($endMonth, 'P1D', null, false);
		$sumCurrentMonthQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS debit', 'SUM(IF(account_target > 0, entry_value, 0)) AS credit']);
		$sumCurrentMonthQuery->from('fin_account_entry')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
		$sumCurrentMonthQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_FIN_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_FIN_ACCOUNT_NONE]]);
		$sumCurrentMonthQuery->andWhere(['>=', 'entry_date', $beginMonth->format(DateTimeUtils::FM_DB_DATE)]);
		$sumCurrentMonthQuery->andWhere(['<=', 'entry_date', $endMonth->format(DateTimeUtils::FM_DB_DATE)]);
		$sumCurrentMonthData = $sumCurrentMonthQuery->createCommand()->queryOne();

		// sum Debit Amount & Credit Amount
		$sumEntryValue = false;
		// query for dataprovider
		$dataQuery = null;
		if ($searchModel->validate()) {
			$t2leftJoin = '';
			$t2leftJoin .= ' (';
			$t2leftJoin .= ' t1.entry_date = t2.opening_date AND t2.delete_flag = :deleteFlagFalse';
			$t2leftJoin .= '   AND (';
			$t2leftJoin .= '     (t1.entry_status = :entryTypeDeposit AND t2.add_flag = :trantypeAdding AND t1.account_source = t2.current_assets AND t1.account_target = t2.saving_account)';
			$t2leftJoin .= '     OR';
			$t2leftJoin .= '     (t1.entry_status = :entryTypeDeposit AND t2.add_flag = :trantypeWithdrawal AND t1.account_source = t2.saving_account AND t1.account_target = t2.current_assets)';
			$t2leftJoin .= '     OR';
			$t2leftJoin .= '     (t1.entry_status = :entryTypeInterestDeposit AND t1.account_source = :accountNoneId AND t1.account_target = t2.saving_account)';
			$t2leftJoin .= '   )';
			$t2leftJoin .= ' )';
			$dataQuery = FinAccountEntry::find()->select('t1.*, t2.transactions_id AS time_deposit_tran_id')->from('fin_account_entry t1')
				->leftJoin('fin_time_deposit_tran t2', $t2leftJoin, [
					'entryTypeDeposit' => MasterValueUtils::MV_FIN_ENTRY_TYPE_DEPOSIT,
					'entryTypeInterestDeposit' => MasterValueUtils::MV_FIN_ENTRY_TYPE_INTEREST_DEPOSIT,
					'accountNoneId' => MasterValueUtils::MV_FIN_ACCOUNT_NONE,
					'deleteFlagFalse' => MasterValueUtils::MV_FIN_FLG_DELETE_FALSE,
					'trantypeAdding' => MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_ADDING,
					'trantypeWithdrawal' => MasterValueUtils::MV_FIN_TIMEDP_TRANTYPE_WITHDRAWAL,
				])->where(['=', 't1.delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
			$sumEntryQuery = (new Query())->select(['SUM(IF(account_source > 0, entry_value, 0)) AS entry_source', 'SUM(IF(account_target > 0, entry_value, 0)) AS entry_target']);
			$sumEntryQuery->from('fin_account_entry')->where(['=', 'delete_flag', MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
			$sumEntryQuery->andWhere(['OR', ['=', 'account_source', MasterValueUtils::MV_FIN_ACCOUNT_NONE], ['=', 'account_target', MasterValueUtils::MV_FIN_ACCOUNT_NONE]]);
			
			if (!empty($searchModel->entry_date_from)) {
				$dataQuery->andWhere(['>=', 't1.entry_date', $searchModel->entry_date_from]);
				$sumEntryQuery->andWhere(['>=', 'entry_date', $searchModel->entry_date_from]);
			}
			if (!empty($searchModel->entry_date_to)) {
				$dataQuery->andWhere(['<=', 't1.entry_date', $searchModel->entry_date_to]);
				$sumEntryQuery->andWhere(['<=', 'entry_date', $searchModel->entry_date_to]);
			}
			if ($searchModel->account_source > 0) {
				$dataQuery->andWhere(['=', 't1.account_source', $searchModel->account_source]);
			}
			if ($searchModel->account_target > 0) {
				$dataQuery->andWhere(['=', 't1.account_target', $searchModel->account_target]);
			}
			$dataQuery->orderBy('t1.entry_date DESC, t1.create_date DESC');
			$sumEntryValue = $sumEntryQuery->createCommand()->queryOne();
		} else {
			$dataQuery = FinAccountEntry::find()->where(['entry_id'=>-1]);
		}
		
		// render GUI
		$renderData = ['searchModel'=>$searchModel, 'phpFmShortDate'=>$phpFmShortDate, 'arrEntryLog'=>$arrEntryLog,
			'arrFinAccount'=>$arrFinAccount, 'dataQuery'=>$dataQuery, 'sumEntryValue'=>$sumEntryValue, 'sumCurrentMonthData'=>$sumCurrentMonthData];
		
		return $this->render('index', $renderData);
	}
	
	public function actionView($id) {
		$this->objectId = $id;
		$model = FinAccountEntry::findOne(['entry_id'=>$id, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
		
		$renderView = 'view';
		if (is_null($model)) {
			$model = false;
			$renderData = ['model'=>$model];
			Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Payment')]));
		} else {
			// master value
			$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
			$arrEntryLog = MasterValueUtils::getArrData('fin_entry_log');
			$arrEntryLogVal = StringUtils::unserializeArr($model->description);
			$model->description = StringUtils::showArrValueAsString($arrEntryLogVal, $arrEntryLog);
			$arrFinAccount = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name']), 'account_id', 'account_name');

			// data for rendering
			$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrFinAccount'=>$arrFinAccount];
		}
		
		// render GUI
		return $this->render($renderView, $renderData);
	}
	
	public function actionCreate() {
		$model = new FinAccountEntry();
		$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
		$arrFinAccount = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
				->where(['delete_flag'=>0, 'account_type'=>[1,2,3,5]])
				->orderBy('account_type, order_num'), 'account_id', 'account_name');
		$arrEntryLog = MasterValueUtils::getArrData('fin_entry_log');
		
		// submit data
		$postData = Yii::$app->request->post();
		$submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
		
		// populate model attributes with user inputs
		$model->load($postData);
		if (is_null($model->arr_entry_log)) {
			$model->arr_entry_log = StringUtils::unserializeArr($model->description);
		}
		
		// init value
		FinAccountEntry::$_PHP_FM_SHORTDATE = $phpFmShortDate;
		$model->scenario = MasterValueUtils::SCENARIO_CREATE;
		if (empty($model->entry_date)) {
			$model->entry_date = DateTimeUtils::formatNow($phpFmShortDate);
		}
		
		// render GUI
		$renderView = 'create';
		$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrFinAccount'=>$arrFinAccount, 'arrEntryLog'=>$arrEntryLog];
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
					$result = $this->createPayment($model);
					if ($result === true) {
						Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Payment')]));
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
	 * create a payment
	 * @param type $paymentModel
	 * @throws Exception
	 * @return string|true
	 */
	private function createPayment($paymentModel) {
		$transaction = Yii::$app->db->beginTransaction();
		$save = true;
		$message = null;
		
		// begin transaction
		try {
			$paymentModel->description = serialize($paymentModel->arr_entry_log);
			$accountSource = FinAccount::findOne($paymentModel->account_source);
			$accountTarget = FinAccount::findOne($paymentModel->account_target);
			// save source
			if (!is_null($accountSource) && ($save !== false)) {
				$accountSource->opening_balance -= $paymentModel->entry_value;
				$save = $accountSource->save();
			}
			// save Target
			if (!is_null($accountTarget) && ($save !== false)) {
				$accountTarget->opening_balance += $paymentModel->entry_value;
				$save = $accountTarget->save();
			}
			// save payment
			if ($save !== false) {
				$save = $paymentModel->save();
			}
			// save note
			if ($save !== false && count($paymentModel->arr_entry_log) > 0) {
				$startDateNote = $paymentModel->entry_date . ' 00:00:00';
				OthNote::updateAll(['start_date'=>$startDateNote], ['entry_log'=>$paymentModel->arr_entry_log]);
			}
		} catch(Exception $e) {
			$save = false;
			$message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Payment')]);
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
		$model = FinAccountEntry::findOne(['entry_id'=>$id, 'entry_status'=>MasterValueUtils::MV_FIN_ENTRY_TYPE_SIMPLE, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
		
		$renderView = 'update';
		if (is_null($model)) {
			$model = false;
			$renderData = ['model'=>$model];
			Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Payment')]));
		} else {
			// back up data
			$model->account_source_old = $model->account_source;
			$model->account_target_old = $model->account_target;
			// master value
			$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
			$arrFinAccount = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
					->where(['delete_flag'=>0, 'account_type'=>[1,2,3,5]])
					->orderBy('account_type, order_num'), 'account_id', 'account_name');
			$arrEntryLog = MasterValueUtils::getArrData('fin_entry_log');
			// submit data
			$postData = Yii::$app->request->post();
			$submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
			
			// populate model attributes with user inputs
			$model->load($postData);
			if (is_null($model->arr_entry_log)) {
				$model->arr_entry_log = StringUtils::unserializeArr($model->description);
			}
			if (empty($model->entry_adjust)) {
				$model->entry_adjust = 0;
			}
			$model->entry_updated = $model->entry_value + $model->entry_adjust;

			// init value
			FinAccountEntry::$_PHP_FM_SHORTDATE = $phpFmShortDate;
			$model->scenario = MasterValueUtils::SCENARIO_UPDATE;
			$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrFinAccount'=>$arrFinAccount, 'arrEntryLog'=>$arrEntryLog];
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
						$result = $this->updatePayment($model);
						if ($result === true) {
							Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Payment')]));
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
	 * update a payment
	 * @param type $paymentModel
	 * @throws Exception
	 * @return string|true
	 */
	private function updatePayment($paymentModel) {
		$transaction = Yii::$app->db->beginTransaction();
		$save = true;
		$message = null;
		
		// begin transaction
		try {
			$entryValueOld = $paymentModel->entry_value;
			$paymentModel->description = serialize($paymentModel->arr_entry_log);
			$paymentModel->entry_value = $paymentModel->entry_updated;
			// save payment
			if ($save) {
				$save = $paymentModel->update();
			}
			// save source
			if ($paymentModel->account_source == $paymentModel->account_source_old) {
				if ($paymentModel->account_source != 0 && $save !== false) {
					$accountSource = FinAccount::findOne($paymentModel->account_source);
					$accountSource->opening_balance -= $paymentModel->entry_adjust;
					$save = $accountSource->update();
				}
			} else {
				// old source
				if ($paymentModel->account_source_old != 0 && $save !== false) {
					$accountSource = FinAccount::findOne($paymentModel->account_source_old);
					$accountSource->opening_balance += $entryValueOld;
					$save = $accountSource->update();
				}
				// new source
				if ($paymentModel->account_source != 0 && $save !== false) {
					$accountSource = FinAccount::findOne($paymentModel->account_source);
					$accountSource->opening_balance -= $paymentModel->entry_value;
					$save = $accountSource->update();
				}
			}
			// save Target
			if ($paymentModel->account_target == $paymentModel->account_target_old) {
				if ($paymentModel->account_target != 0 && $save !== false) {
					$accountTarget = FinAccount::findOne($paymentModel->account_target);
					$accountTarget->opening_balance += $paymentModel->entry_adjust;
					$save = $accountTarget->update();
				}
			} else {
				// old target
				if ($paymentModel->account_target_old != 0 && $save !== false) {
					$accountTarget = FinAccount::findOne($paymentModel->account_target_old);
					$accountTarget->opening_balance -= $entryValueOld;
					$save = $accountTarget->update();
				}
				// new target
				if ($paymentModel->account_target != 0 && $save !== false) {
					$accountTarget = FinAccount::findOne($paymentModel->account_target);
					$accountTarget->opening_balance += $paymentModel->entry_value;
					$save = $accountTarget->update();
				}
			}
			// save note
			if ($save !== false && count($paymentModel->arr_entry_log) > 0) {
				$startDateNote = $paymentModel->entry_date . ' 00:00:00';
				OthNote::updateAll(['start_date'=>$startDateNote], ['entry_log'=>$paymentModel->arr_entry_log]);
			}
		} catch(Exception $e) {
			$save = false;
			$message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Payment')]);
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
		$model = FinAccountEntry::findOne(['entry_id'=>$id, 'entry_status'=>MasterValueUtils::MV_FIN_ENTRY_TYPE_SIMPLE, 'delete_flag'=>MasterValueUtils::MV_FIN_FLG_DELETE_FALSE]);
		
		$renderView = 'copy';
		if (is_null($model)) {
			$model = false;
			$renderData = ['model'=>$model];
			Yii::$app->session->setFlash(MasterValueUtils::FLASH_ERROR, Yii::t('common', 'The requested {record} does not exist.', ['record'=>Yii::t('fin.models', 'Payment')]));
		} else {
			// master value
			$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
			$arrFinAccount = ModelUtils::getArrData(FinAccount::find()->select(['account_id', 'account_name'])
					->where(['delete_flag'=>0, 'account_type'=>[1,2,3,5]])
					->orderBy('account_type, order_num'), 'account_id', 'account_name');
			$arrEntryLog = MasterValueUtils::getArrData('fin_entry_log');
			// reset value
			$model->entry_date = DateTimeUtils::formatNow($phpFmShortDate);
			
			// submit data
			$postData = Yii::$app->request->post();
			$submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
			
			// populate model attributes with user inputs
			$model->load($postData);
			if (is_null($model->arr_entry_log)) {
				$model->arr_entry_log = StringUtils::unserializeArr($model->description);
			}
			
			// init value
			FinAccountEntry::$_PHP_FM_SHORTDATE = $phpFmShortDate;
			$model->scenario = MasterValueUtils::SCENARIO_COPY;
			$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrFinAccount'=>$arrFinAccount, 'arrEntryLog'=>$arrEntryLog];
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
						$result = $this->copyPayment($model);
						if ($result === true) {
							Yii::$app->session->setFlash(MasterValueUtils::FLASH_SUCCESS, Yii::t('common', '{record} has been saved successfully.', ['record'=>Yii::t('fin.models', 'Payment')]));
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
	 * copy a payment
	 * @param type $paymentModel
	 * @throws Exception
	 * @return string|true
	 */
	private function copyPayment($paymentModel) {
		$paymentModel->setIsNewRecord(true);
		$transaction = Yii::$app->db->beginTransaction();
		$save = true;
		$message = null;
		
		// begin transaction
		try {
			$paymentModel->description = serialize($paymentModel->arr_entry_log);
			$accountSource = FinAccount::findOne($paymentModel->account_source);
			$accountTarget = FinAccount::findOne($paymentModel->account_target);
			// save source
			if (!is_null($accountSource) && ($save !== false)) {
				$accountSource->opening_balance -= $paymentModel->entry_value;
				$save = $accountSource->save();
			}
			// save Target
			if (!is_null($accountTarget) && ($save !== false)) {
				$accountTarget->opening_balance += $paymentModel->entry_value;
				$save = $accountTarget->save();
			}
			// save payment
			if ($save !== false) {
				$save = $paymentModel->save();
			}
		} catch(Exception $e) {
			$save = false;
			$message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Payment')]);
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