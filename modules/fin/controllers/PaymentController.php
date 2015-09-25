<?php
namespace app\modules\fin\controllers;

use Yii;
use app\components\DateTimeUtils;
use app\components\ModelUtils;
use app\components\MasterValueUtils;
use app\controllers\MobiledetectController;
use app\models\FinAccount;
use app\models\FinAccountEntry;

class PaymentController extends MobiledetectController {
	public function actionIndex() {
		return $this->render('index');
	}
	
	public function actionCreate() {
		$model = new FinAccountEntry();
		$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
		$arrFinAccount = ModelUtils::getArrData(FinAccount::find(), 'account_id', 'account_name', ['delete_flag'=>0, 'account_type'=>[1,2,3,5]], 'account_type, order_num');
		
		// submit data
		$postData = Yii::$app->request->post();
		$submitMode = isset($postData[MasterValueUtils::SM_MODE_NAME]) ? $postData[MasterValueUtils::SM_MODE_NAME] : false;
		
		// populate model attributes with user inputs
		$model->load($postData);
		
		// init value
		FinAccountEntry::$_PHP_FM_SHORTDATE = $phpFmShortDate;
		$model->scenario = FinAccountEntry::SCENARIO_CREATE;
		if (empty($model->entry_date)) {
			$model->entry_date = DateTimeUtils::formatNow($phpFmShortDate);
		}
		
		// render GUI
		$renderView = 'create';
		$renderData = ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrFinAccount'=>$arrFinAccount];
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
						Yii::$app->getResponse()->redirect(array('fin/payment/index'));
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
	 * @return string|true
	 */
	private function createPayment($paymentModel) {
		$transaction = Yii::$app->db->beginTransaction();
		$save = true;
		$message = null;
		
		// begin transaction
		try {
			$accountSource = FinAccount::findOne($paymentModel->account_source);
			$accountTarget = FinAccount::findOne($paymentModel->account_target);
			// save source
			if (!is_null($accountSource) && $save) {
				$accountSource->opening_balance -= $paymentModel->entry_value;
				$save = $accountSource->save();
			}
			// save Target
			if (!is_null($accountTarget) && $save) {
				$accountTarget->opening_balance += $paymentModel->entry_value;
				$save = $accountTarget->save();
			}
			// save payment
			if ($save) {
				$save = $paymentModel->save();
			}
		} catch(\Exception $e) {
			$save = false;
			$message = Yii::t('common', 'Unable to save {record}.', ['record'=>Yii::t('fin.models', 'Payment')]);
		}
		
		// end transaction
		try {
			if ($save) {
				$transaction->commit();
			} else {
				$transaction->rollback();
				return $message;
			}
		} catch(\Exception $e) {
			throw \Exception(Yii::t('common', 'Unable to excute Transaction.'));
		}
		
		return true;
	}
	
	public function actionUpdate() {
		
	}
}
?>