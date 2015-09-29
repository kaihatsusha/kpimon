<?php
namespace app\modules\fin\controllers;

use app\controllers\MobiledetectController;
use app\models\FinAccount;

class DepositController extends MobiledetectController {
	public $defaultAction = 'index';
	
	public function behaviors() {
		return [
			'access' => [
				'class' => \yii\filters\AccessControl::className(),
				'only' => ['index'],
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
		
		$arrFinAccount = FinAccount::find()->where(['delete_flag'=>0])->orderBy('order_num')->all();
		foreach ($arrFinAccount as $finAccount) {
			$instance = $finAccount->instance();
			$arrDeposits[] = $instance;
		}
		
		return $this->render('index', ['arrDeposits'=>$arrDeposits]);
	}
}
?>