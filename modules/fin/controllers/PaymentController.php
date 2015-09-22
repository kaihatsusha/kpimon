<?php
namespace app\modules\fin\controllers;

use Yii;
use yii\web\Controller;
use app\components\DateTimeUtils;
use app\components\ModelUtils;
use app\models\FinAccount;
use app\models\FinAccountEntry;

class PaymentController extends Controller {
	public function actionIndex() {
		
	}
	
	public function actionCreate() {
		$model = new FinAccountEntry();
		$phpFmShortDate = DateTimeUtils::getPhpDateFormat();
		$arrFinAccount = ModelUtils::getArrData(FinAccount::find(), 'account_id', 'account_name', ['delete_flag'=>0, 'account_type'=>[1,2,3,5]], 'account_type, order_num');
		//var_dump($arrFinAccount);
		
		// populate model attributes with user inputs
		$model->load(Yii::$app->request->post());
		
		// init value
		FinAccountEntry::$_PHP_FM_SHORTDATE = $phpFmShortDate;
		$model->scenario = FinAccountEntry::SCENARIO_CREATE;
		if (empty($model->entry_date)) {
			$model->entry_date = DateTimeUtils::formatNow($phpFmShortDate);
		}
		//$model->validate();
		//var_dump($model->hasErrors(),Yii::$app->request->post());
		
		return $this->render('create', ['model'=>$model, 'phpFmShortDate'=>$phpFmShortDate, 'arrFinAccount'=>$arrFinAccount]);
	}
	
	public function actionUpdate() {
		/*if ($post === NULL)
        {
        Yii::$app->session->setFlash('error', 'A post with that id does not exist');
        Yii::$app->getResponse()->redirect(array('site/index'));
		 * 
		 <div class="alert alert-error">
        <?php echo Yii::$app->session->getFlash('error'); ?>
        </div>
        <?php endif; ?>
 
        <?php if(Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
        <?php echo Yii::$app->session->getFlash('success'); ?>
        </div>
		 * 
		 
		         $model = new Post;
        if ($this->populate($_POST, $model) && $model->save())
        Yii::$app->response->redirect(array('site/read', 'id' => $model->id));
 
        echo $this->render('create', array(
        'model' => $model
        ));
        }*/
	}
}
?>