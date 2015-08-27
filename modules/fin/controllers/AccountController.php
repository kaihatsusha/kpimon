<?php
namespace app\modules\fin\controllers;

use yii\web\Controller;
use app\models\FinAccount;
use \app\models\extended\FinAccount01I00;
use \app\models\extended\FinAccount02I00;
use \app\models\extended\FinAccount03I00;
use \app\models\extended\FinAccount04I00;
use \app\models\extended\FinAccount05I00;
use \app\models\extended\FinAccount06I00;

class AccountController extends Controller {
	public function actionIndex() {
		$arrDeposits = array();
		
		$arrFinAccount = FinAccount::find()->where(['delete_flag'=>0])->orderBy('order_num')->all();
		foreach ($arrFinAccount as $finAccount) {
			$instance = $finAccount->instance();
			if ($instance instanceof FinAccount01I00) {
				
			} elseif ($instance instanceof FinAccount02I00) {
				
			} elseif ($instance instanceof FinAccount03I00) {
				
			} elseif ($instance instanceof FinAccount04I00) {
				$arrDeposits[] = $instance;
			} elseif ($instance instanceof FinAccount05I00) {
				
			} elseif ($instance instanceof FinAccount06I00) {
				
			}
			//var_dump($finAccount->instance());
		}
		//var_dump($arrDeposits);die;
		return $this->render('index', ['arrDeposits'=>$arrDeposits]);
	}
}
?>