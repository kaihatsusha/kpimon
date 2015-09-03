<?php
namespace app\modules\fin\controllers;

use yii\web\Controller;
use app\models\FinAccount;

class DepositController extends Controller {
	public function actionIndex() {
		$arrDeposits = [];
		
		$arrFinAccount = FinAccount::find()->where(['delete_flag'=>0])->orderBy('order_num')->all();
		foreach ($arrFinAccount as $finAccount) {
			$instance = $finAccount->instance();
			$arrDeposits[] = $instance;
		}
		
		return $this->render('index', ['arrDeposits'=>$arrDeposits]);
	}
}
?>